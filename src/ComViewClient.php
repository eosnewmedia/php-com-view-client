<?php
declare(strict_types=1);

namespace Eos\ComView\Client;

use Eos\ComView\Client\Model\CommandRequest;
use Eos\ComView\Client\Model\CommandResponse;
use Eos\ComView\Client\Model\ViewRequest;
use Eos\ComView\Client\Model\ViewResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @author Philipp Marien <marien@eosnewmedia.de>
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
     * @throws Throwable
     */
    public function requestView(ViewRequest $viewRequest): ViewResponse
    {
        $query = [];
        if (count($viewRequest->getParameters()) > 0) {
            $query['parameters'] = $viewRequest->getParameters();
        }
        if (count($viewRequest->getPagination()) > 0) {
            $query['pagination'] = $viewRequest->getPagination();
        }
        if ($viewRequest->getOrderBy()) {
            $query['orderBy'] = $viewRequest->getOrderBy();
        }

        $requestUri = $this->uriFactory->createUri(rtrim($this->baseUrl, '/') . '/cv/' . $viewRequest->getName());
        $requestUri->withQuery(http_build_query($query));

        $request = $this->generateRequest('GET', $requestUri, $viewRequest->getHeaders());
        $response = $this->httpClient->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);

        $viewResponse = new ViewResponse(
            $response->getHeaders(),
            array_key_exists('parameters', $responseData) ? $responseData['parameters'] : [],
            array_key_exists('pagination', $responseData) ? $responseData['pagination'] : [],
            $parameters['orderBy'] ?? null,
            array_key_exists('data', $responseData) ? $responseData['data'] : [],
            $response->getStatusCode()
        );

        return $viewResponse;
    }

    /**
     * @param CommandRequest $commandRequest
     * @return CommandResponse
     * @throws Throwable
     */
    public function executeCommands(CommandRequest $commandRequest): CommandResponse
    {
        $body = [];
        foreach ($commandRequest->getCommands() as $command) {
            $body[$command->getId()] = [
                'command' => $command->getCommand(),
                'parameters' => $command->getParameters()
            ];
        }

        $requestUri = $this->uriFactory->createUri(rtrim($this->baseUrl, '/') . '/cv/execute');
        $request = $this->generateRequest('POST', $requestUri, $commandRequest->getHeaders(), $body);

        $response = $this->httpClient->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);

        $commandResponse = new CommandResponse($response->getHeaders(), $commandRequest->getCommands());
        foreach ((array)$responseData as $id => $result) {
            $commandResponse->getCommand($id)->markExecuted(
                $result['status'],
                array_key_exists('result', $result) ? $result['result'] : []
            );
        }

        return $commandResponse;
    }

    /**
     * @param string $method
     * @param UriInterface $requestUri
     * @param array $headers
     * @param array|null $content
     * @return RequestInterface
     * @throws Throwable
     */
    private function generateRequest(
        string $method,
        UriInterface $requestUri,
        array $headers,
        ?array $content = null
    ): RequestInterface {
        $request = $this->requestFactory->createRequest($method, $requestUri)
            ->withHeader('Content-Type', 'application/json');

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($content !== null) {
            $request = $request->withBody(
                $this->streamFactory->createStream(json_encode($content))
            );
        }

        return $request;
    }
}
