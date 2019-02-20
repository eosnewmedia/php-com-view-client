<?php
declare(strict_types=1);

namespace Eos\ComView\Client;

use Eos\ComView\Client\Exception\ComViewException;
use Eos\ComView\Client\Model\Value\CommandRequest;
use Eos\ComView\Client\Model\Value\CommandResponse;
use Eos\ComView\Client\Model\Value\ViewRequest;
use Eos\ComView\Client\Model\Value\ViewResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ComViewClient
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @param ClientInterface $httpClient
     * @param string $baseUrl
     * @param UriFactoryInterface $uriFactory
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        string $baseUrl,
        ClientInterface $httpClient,
        UriFactoryInterface $uriFactory,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
        $this->uriFactory = $uriFactory;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param ViewRequest $viewRequest
     * @return ViewResponse
     * @throws ComViewException
     */
    public function requestView(ViewRequest $viewRequest): ViewResponse
    {
        try {
            $query = [];
            if (\count($viewRequest->getParameters()) > 0) {
                $query['parameters'] = $viewRequest->getParameters();
            }
            if (\count($viewRequest->getPagination()) > 0) {
                $query['pagination'] = $viewRequest->getPagination();
            }
            if ($viewRequest->getOrderBy()) {
                $query['orderBy'] = $viewRequest->getOrderBy();
            }

            $requestUri = $this->uriFactory->createUri(rtrim($this->baseUrl, '/') . '/cv/' . $viewRequest->getName());
            $requestUri->withQuery(http_build_query($query));

            $request = $this->generateRequest('GET', $requestUri);
            $response = $this->httpClient->sendRequest($request);
            $responseData = json_decode($response->getBody()->getContents(), true);

            $viewResponse = new ViewResponse(
                \array_key_exists('parameters', $responseData) ? $responseData['parameters'] : [],
                \array_key_exists('pagination', $responseData) ? $responseData['pagination'] : [],
                $parameters['orderBy'] ?? null,
                \array_key_exists('data', $responseData) ? $responseData['data'] : [],
                $response->getStatusCode()
            );

            return $viewResponse;
        } catch (\Throwable $exception) {
            throw new ComViewException('An Error occurred while performing this request', 0, $exception);
        }
    }

    /**
     * @param CommandRequest $commandRequest
     * @return CommandResponse
     * @throws ComViewException
     */
    public function executeCommand(CommandRequest $commandRequest): CommandResponse
    {
        $id = md5(uniqid('command', true));

        try {
            $response = $this->handleCommandRequest(
                [
                    $id => [
                        'command' => $commandRequest->getCommand(),
                        'parameters' => $commandRequest->getParameters()
                    ]
                ]
            );

            if (array_key_exists($id, $response)) {
                return $response[$id];
            }

            throw new \RuntimeException('Invalid response.');
        } catch (\Throwable $exception) {
            throw new ComViewException('An Error occurred while performing this request', 0, $exception);
        }
    }

    /**
     * @param CommandRequest[] $commandRequests
     * @return CommandResponse[]
     * @throws ComViewException
     */
    public function executeCommands(array $commandRequests): array
    {
        try {
            $body = [];
            foreach ($commandRequests as $id => $commandRequest) {
                $body[(string)$id] = [
                    'command' => $commandRequest->getCommand(),
                    'parameters' => $commandRequest->getParameters()
                ];
            }

            return $this->handleCommandRequest($body);
        } catch (\Throwable $exception) {
            throw new ComViewException('An Error occurred while performing this request', 0, $exception);
        }
    }

    /**
     * @param string $method
     * @param UriInterface $requestUri
     * @param array|null $content
     * @return RequestInterface
     * @throws \Exception
     */
    private function generateRequest(string $method, UriInterface $requestUri, ?array $content = null): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $requestUri);
        $request->withHeader('Content-Type', 'application/json');
        if ($content !== null) {
            $request->withBody(
                $this->streamFactory->createStream(json_encode($content))
            );
        }

        return $request;
    }

    /**
     * @param array $body
     * @return CommandResponse[]
     * @throws \Throwable
     */
    private function handleCommandRequest(array $body): array
    {
        $requestUri = $this->uriFactory->createUri($this->baseUrl . '/cv/execute');
        $request = $this->generateRequest('POST', $requestUri, $body);

        $response = $this->httpClient->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);
        $commandResponse = [];

        foreach ((array)$responseData as $id => $response) {
            $commandResponse[(string)$id] = new CommandResponse(
                $responseData['status'],
                \array_key_exists('result', $responseData) ? $responseData['result'] : []
            );
        }

        return $commandResponse;
    }
}
