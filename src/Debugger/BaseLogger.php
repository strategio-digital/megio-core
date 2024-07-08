<?php
declare(strict_types=1);

namespace Megio\Debugger;

use Megio\Helper\Path;
use Megio\Mailer\SmtpMailer;
use Megio\Storage\Adapter\S3Storage;
use Nette\Mail\Message;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tracy\BlueScreen;
use Tracy\ILogger;

abstract class BaseLogger implements ILogger
{
    protected ?string $tracyUri = null;
    
    protected function createErrorHash(\Throwable $throwable): string
    {
        return md5($throwable->getMessage() . $throwable->getFile() . $throwable->getLine() . $throwable->getTraceAsString());
    }
    
    protected function createBlueScreen(\Throwable $message, string $fileName): void
    {
        (new BlueScreen)->renderToFile($message, Path::logDir() . '/' . $fileName);
    }
    
    protected function createBlueScreenFileName(string $hash): string
    {
        return 'blue-screen-' . $hash . '.html';
    }
    
    protected function uploadBlueScreenToS3OnEnvEnabled(string $fileName): void
    {
        $filePathName = Path::logDir() . "/{$fileName}";
        
        if (array_key_exists('LOG_S3_BLUESCREEN', $_ENV)
            && mb_strtolower($_ENV['LOG_S3_BLUESCREEN']) === 'true'
            && file_exists($filePathName)
        ) {
            $storage = new S3Storage();
            if (count($storage->list(".tracy/{$fileName}")) === 0) {
                $file = new UploadedFile($filePathName, $fileName, 'text/html');
                $storage->upload($file, ".tracy/", false);
            }
        }
    }
    
    /**
     * @return array<string, string>
     */
    protected function createTracyPayload(\Throwable $throwable): array
    {
        $hash = $this->createErrorHash($throwable);
        $fileName = $this->createBlueScreenFileName($hash);
        $link = $this->tracyUri ? ($this->tracyUri . $hash) : ($_ENV['APP_URL'] . '/app/logs/tracy/' . $hash);
        
        return [
            'hash' => $hash,
            'filename' => $fileName,
            'link' => $link
        ];
    }
    
    /**
     * @param array $message
     * @param string $level
     * @param \DateTime $now
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    protected function createBasicPayload(string $message, string $level, \DateTime $now, array $context): array
    {
        return [
            '@timestamp' => $now->format('Y-m-d\TH:i:s.uP'),
            '@version' => 1,
            'host' => array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : null,
            'message' => $message,
            'type' => $_ENV['APP_ENVIRONMENT'],
            'channel' => 'default',
            'level' => $level,
            'context' => $context,
        ];
    }
    
    /**
     * @param array<string, mixed> $context
     */
    protected function sendMailOnEnvEnabled(array $context, string $level, \DateTime $now): void
    {
        if (array_key_exists('LOG_MAIL', $_ENV) && $_ENV['LOG_MAIL'] !== '' && !in_array($level, [self::DEBUG, self::INFO])) {
            $utcTime = $now->format('Y-m-d\TH:i:s.uP');
            $snooze = strtotime('1 day') - time();
            
            $body = array_map(function ($key, $value) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
                return "{$key}: {$value}";
            }, array_keys($context), $context);
            
            
            if (@filemtime(Path::logDir() . '/email-sent') + $snooze < time() // @ file may not exist
                && @file_put_contents(Path::logDir() . '/email-sent', $utcTime) // @ file may not be writable
            ) {
                $message = new Message();
                $message->setFrom($_ENV['SMTP_SENDER'], $_ENV['APP_NAME']);
                $message->addTo($_ENV['LOG_MAIL']);
                $message->setSubject("[LOG] {$_ENV['APP_NAME']} | {$utcTime}");
                $message->setBody("Application {$_ENV['APP_NAME']} just crashed.\r\n\r\n" . implode("\r\n", $body));
                
                (new SmtpMailer())->send($message);
            }
        }
    }
}