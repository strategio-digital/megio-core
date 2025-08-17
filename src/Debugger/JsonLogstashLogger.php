<?php
declare(strict_types=1);

namespace Megio\Debugger;

use Megio\Helper\Path;
use Nette\Utils\DateTime;

class JsonLogstashLogger extends BaseLogger
{
    public function __construct(protected ?string $tracyUri = null)
    {
    }
    
    /**
     * @param mixed $message
     * @param string $level
     * @return void
     */
    public function log(mixed $message, string $level = self::INFO): void
    {
        $now = new DateTime();
        $date = $now->format('Y-m-d');
        $payload = $this->formatPayload($message, $level, $now);
        
        /** @var non-empty-string $json */
        $json = json_encode($payload);
        $filePathName = Path::logDir() . "/{$date}--logstash.json.log";
        
        if (!@file_put_contents($filePathName, $json . PHP_EOL, FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException("Unable to write to log file '{$filePathName}'. Is directory writable?");
        }
        
        if ($message instanceof \Throwable) {
            $hash = $this->createErrorHash($message);
            $fileName = $this->createBlueScreenFileName($hash);
            
            $this->createBlueScreen($message, $fileName);
            $this->uploadBlueScreenToS3OnEnvEnabled($fileName);
            $this->sendMailOnEnvEnabled($payload, $level, $now);
        }
    }
    
    /**
     * @param mixed $message
     * @param string $level
     * @param \DateTime $now
     * @return array<string, mixed>
     */
    protected function formatPayload(mixed $message, string $level, \DateTime $now): array
    {
        $messageString = 'Unknown message';
        if ($message instanceof \Throwable) {
            $messageString = $message->getMessage();
        } else if (is_array($message) && array_key_exists('message', $message)) {
            $messageString = $message['message'];
            unset($message['message']);
        } else if (is_string($message)) {
            $messageString = $message;
        }
        
        $ctx = $message instanceof \Throwable
            ? ['tracy' => $this->createTracyPayload($message)]
            : (is_string($message) ? [] : $message);
        
        $payload = $this->createBasicPayload($messageString, $level, $now, $ctx);
        
        // Mask sensitive data in context
        if (is_array($payload['context'])) {
            $payload['context'] = $this->maskSensitiveContextData($payload['context']);
        }
        
        return $payload;
    }
    
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function maskSensitiveContextData(array $data): array
    {
        // Mask context.request.headers.Authorization
        if (array_key_exists('request', $data) && is_array($data['request'])) {
            if (array_key_exists('headers', $data['request']) && is_array($data['request']['headers'])) {
                if (array_key_exists('Authorization', $data['request']['headers'])) {
                    $data['request']['headers']['Authorization'] = '****Masked****';
                }
            }
        }
        
        return $data;
    }
}