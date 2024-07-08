<?php
declare(strict_types=1);

namespace Megio\Debugger;

use Sentry\EventId;
use Sentry\Integration\EnvironmentIntegration;
use Sentry\Integration\ErrorListenerIntegration;
use Sentry\Integration\FatalErrorListenerIntegration;
use Sentry\Integration\FrameContextifierIntegration;
use Sentry\Integration\ModulesIntegration;
use Sentry\Integration\RequestIntegration;
use Sentry\Integration\TransactionIntegration;
use Sentry\Severity;
use Sentry\State\Scope;
use Tracy\Dumper;
use Tracy\ILogger;
use function Sentry\addBreadcrumb;
use function Sentry\captureException;
use function Sentry\captureMessage;
use function Sentry\configureScope;

class SentryLogger extends BaseLogger
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        protected array   $options = [],
        protected ?string $tracyUri = null
    )
    {
        \Sentry\init(array_merge([
            'dsn' => $_ENV['LOG_SENTRY_DSN'],
            'environment' => $_ENV['APP_ENVIRONMENT'],
            'attach_stacktrace' => true,
            'traces_sample_rate' => 1.0,
            'profiles_sample_rate' => 1.0,
            'default_integrations' => false,
            'send_default_pii' => true,
            'integrations' => [
                new ErrorListenerIntegration(),
                //new ExceptionListenerIntegration(),
                new FatalErrorListenerIntegration(),
                new RequestIntegration(),
                new EnvironmentIntegration(),
                new FrameContextifierIntegration(),
                new TransactionIntegration(),
                new ModulesIntegration(),
            ],
        ], $options));
    }
    
    public function log(mixed $message, string $level = self::INFO): ?EventId
    {
        $now = new \DateTime();
        
        if ($message instanceof \Throwable) {
            $hash = $this->createErrorHash($message);
            $fileName = $this->createBlueScreenFileName($hash);
            $tracy = $this->createTracyPayload($message);
            
            $this->createBlueScreen($message, $fileName);
            $this->uploadBlueScreenToS3OnEnvEnabled($fileName);
            $this->addTracyContext($tracy);
            
            $payload = $this->createBasicPayload($message->getMessage(), $level, $now, ['tracy' => $tracy]);
            $this->sendMailOnEnvEnabled($payload, $level, $now);
            
            return captureException($message);
        }
        
        if (is_array($message)) {
            $title = array_key_exists('message', $message) ? $message['message'] : 'Unknown message';
            unset($message['message']);
            $this->capturePayload($message);
            return $this->captureMessage($title, $level);
        }
        
        return $this->captureMessage(is_string($message) ? $message : Dumper::toText($message), $level);
    }
    
    /**
     * @param array<string, mixed> $payload
     */
    protected function capturePayload(array $payload): void
    {
        addBreadcrumb('debugger', null, $payload, \Sentry\Breadcrumb::LEVEL_DEBUG);
    }
    
    protected function captureMessage(string $message, string $level = ILogger::ERROR): ?EventId
    {
        $severity = $this->mapSeverity($level);
        return captureMessage($message, $severity);
    }
    
    /**
     * @param array<string, string> $tracy
     * @return void
     */
    protected function addTracyContext(array $tracy): void
    {
        configureScope(function (Scope $scope) use ($tracy): void {
            $scope->setContext('tracy', $tracy);
        });
    }
    
    protected function mapSeverity(string $level = ILogger::ERROR): Severity
    {
        $level = mb_strtolower($level);
        
        $levelMap = [
            self::DEBUG => Severity::debug(),
            self::INFO => Severity::info(),
            self::WARNING => Severity::warning(),
            self::ERROR => Severity::error(),
            self::EXCEPTION => Severity::error(),
            self::CRITICAL => Severity::fatal(),
        ];
        
        return array_key_exists($level, $levelMap) ? $levelMap[$level] : Severity::fatal();
    }
}