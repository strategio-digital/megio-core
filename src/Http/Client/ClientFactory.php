<?php
declare(strict_types=1);

namespace Megio\Http\Client;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\TransferStats;
use Megio\Helper\Path;
use Psr\Http\Message\RequestInterface;
use Tracy\Debugger;

use const FILE_APPEND;
use const LOCK_EX;
use const PHP_EOL;

class ClientFactory
{
    /** @var array<int, TransferStats> */
    public static array $history = [];

    protected string $loggerName;

    protected HandlerStack $stack;

    public function __construct()
    {
        $this->stack = HandlerStack::create();
        $this->stack->push($this->loggerMiddleware(), 'logger');
    }

    /**
     * @param array<string, mixed> $config
     */
    public function create(string $loggerName, array $config = []): Client
    {
        $this->loggerName = $loggerName;

        if (!array_key_exists('handler', $config)) {
            $config['handler'] = $this->stack;
        }

        return new Client($config);
    }

    public function getStack(): HandlerStack
    {
        return $this->stack;
    }

    public function loggerMiddleware(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $options['on_stats'] = function (TransferStats $stats): void {
                    if ($_ENV['APP_ENVIRONMENT'] === 'develop') {
                        self::$history[] = $stats;
                    }

                    $context = $this->formatLogMessage($stats);
                    $fileName = Path::logDir() . '/' . date('Y-m-d') . '_request-response.log';

                    $data = [
                        '@timestamp' => (new DateTime())->format('Y-m-d\TH:i:s.uP'),
                        '@version' => 1,
                        'host' => array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : null,
                        'message' => $this->loggerName,
                        'type' => $_ENV['APP_ENVIRONMENT'],
                        'channel' => 'default',
                        'level' => Debugger::DEBUG,
                        'context' => $context,
                    ];

                    $output = json_encode($data) . PHP_EOL;
                    file_put_contents($fileName, $output, FILE_APPEND | LOCK_EX);

                    // TODO: use next line instead of custom logging (after removed support for Sentry::captureMessage)
                    //Debugger::log(['message' => $this->loggerName, 'context' => $context], Debugger::DEBUG);
                };

                return $handler($request, $options);
            };
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function formatLogMessage(TransferStats $stats): array
    {
        $request = $stats->getRequest();
        $response = $stats->getResponse();

        $rqBody = $request->getBody()->__toString();
        $requestBody = json_validate($rqBody) ? json_decode($rqBody) : $rqBody;

        $rsBody = $response?->getBody()->__toString();
        $responseBody = $rsBody && json_validate($rsBody) ? json_decode($rsBody) : $rsBody;

        $request->getBody()->rewind();
        $response?->getBody()->rewind();

        return [
            'delay' => ($stats->getTransferTime() * 1000) . 'ms',
            'effectiveUri' => $stats->getEffectiveUri()->__toString(),
            'request' => [
                'method' => $request->getMethod(),
                'target' => $request->getRequestTarget(),
                'uri' => $request->getUri()->__toString(),
                'headers' => $request->getHeaders(),
                'body' => $requestBody,
            ],
            'response' => [
                'status' => $response?->getStatusCode(),
                'reasonPhrase' => $response?->getReasonPhrase(),
                'headers' => $response?->getHeaders(),
                'body' => $responseBody,
            ],
        ];
    }
}
