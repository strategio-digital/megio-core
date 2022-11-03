<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Debugger;

use Framework\Mailer\SmtpMailer;
use Framework\Storage\Adapter\S3Storage;
use Nette\Mail\Message;
use Nette\Utils\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tracy\BlueScreen;
use Tracy\Logger as TracyLogger;

class Logger extends TracyLogger
{
    private readonly S3Storage $storage;
    
    public function __construct(?string $directory, string $email = null, ?BlueScreen $blueScreen = null)
    {
        parent::__construct($directory, $email, $blueScreen);
        
        $this->storage = new S3Storage();
    }
    
    public function log($message, $level = self::INFO): string
    {
        $dateTime = new \DateTime();
        $date = $dateTime->format('Y-m-d');
        $time = $dateTime->format('H-i-s');
        
        $context = [
            'level' => $level,
            'timestamp' => $dateTime->format('Y-m-d H:i:s'),
            'message' => $message instanceof \Throwable ? null : $message,
        ];
        
        $bsFilePrefix = $this->makeBlueScreen($message, $level, $date, $time);
        
        if ($bsFilePrefix) {
            $context = array_merge($context, [
                'message' => $message->getMessage(),
                'file' => $message->getFile(),
                'line' => $message->getLine(),
                'code' => $message->getCode(),
                'tracy_bs_prefix' => $bsFilePrefix
            ]);
        }
        
        /** @var string $json */
        $json = json_encode($context);
        $logFilePath = $this->directory . "/{$date}--app-json.log";
        
        if (!@file_put_contents($logFilePath, $json . PHP_EOL, FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException("Unable to write to log file '{$logFilePath}'. Is directory writable?");
        }
        
        if (array_key_exists('LOG_ADAPTER', $_ENV) && mb_strtolower($_ENV['LOG_ADAPTER']) === 's3') {
            $this->storage->put("tracy-logs/{$date}--app-json.log", $json);
        }
        
        if (array_key_exists('LOG_MAIL', $_ENV) && $_ENV['LOG_MAIL'] !== '') {
            $this->sendMail($context);
        }
        
        return "$bsFilePrefix--$date--$time.html";
    }
    
    private function makeBlueScreen(mixed $message, string $level, string $date, string $time): ?string
    {
        if ($message instanceof \Throwable) {
            
            $exceptionFilePath = $this->getExceptionFile($message, $level);
            list($type, , , $hash) = explode('--', (new \SplFileInfo($exceptionFilePath))->getFilename());
            
            $hash = str_replace('.html', '', $hash);
            $bsFilePrefix = "{$hash}--{$type}";
            $bsFileName = "{$bsFilePrefix}--{$date}--{$time}.html";
            $bsFilePath = $this->directory . '/' . $bsFileName;
            
            if (Finder::findFiles("{$hash}--{$type}*.html")->in($this->directory . '/')->count() === 0) {
                $bs = new BlueScreen();
                $bs->renderToFile($message, $bsFilePath);
            }
            
            if (array_key_exists('LOG_ADAPTER', $_ENV) && mb_strtolower($_ENV['LOG_ADAPTER']) === 's3' && file_exists($bsFilePath)) {
                $files = $this->storage->list("tracy-logs/blue-screens/{$bsFilePrefix}");
                if (count($files) === 0) {
                    $file = new UploadedFile($bsFilePath, $bsFileName, 'text/html');
                    $this->storage->upload($file, "tracy-logs/blue-screens/", false);
                }
            }
            
            return $bsFilePrefix;
        }
        
        return null;
    }
    
    /**
     * @param array<string, string> $context
     * @return void
     */
    private function sendMail(array $context): void
    {
        $snooze = strtotime('1 day') - time();
        $body = array_map(function ($key, $value) {
            return "{$key}: {$value}";
        }, array_keys($context), $context);
        
        if (@filemtime($this->directory . '/email-sent') + $snooze < time() // @ file may not exist
            && @file_put_contents($this->directory . '/email-sent', $context['timestamp']) // @ file may not be writable
        ) {
            $message = new Message();
            $message->setFrom($_ENV['SMTP_SENDER'], $_ENV['APP_NAME']);
            $message->addTo($_ENV['LOG_MAIL']);
            $message->setSubject("[LOG] {$_ENV['APP_NAME']} | {$context['timestamp']}");
            $message->setBody("Application {$_ENV['APP_NAME']} just crashed.\r\n\r\n" . implode("\r\n", $body));
            
            (new SmtpMailer())->send($message);
        }
    }
}