<?php
declare(strict_types=1);

namespace Eos\ComView\Client;

use Eos\ComView\Client\Exception\NotFoundException;
use Eos\ComView\Client\Exception\RequestException;
use Eos\ComView\Client\Helper\UuidGeneratorInterface;
use Eos\ComView\Client\HttpClient\ClientInterface;
use Eos\ComView\Client\Model\Common\AbstractCollection;
use Eos\ComView\Client\Model\Common\KeyValueCollection;
use Eos\ComView\Client\Model\Value\CommandRequest;
use Eos\ComView\Client\Model\Value\CommandResponse;
use Eos\ComView\Client\Model\Value\ViewRequest;
use Eos\ComView\Client\Model\Value\ViewResponse;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ComViewClient
{

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param ClientInterface $httpClient
     * @param UuidGeneratorInterface $uuidGenerator
     * @param string $baseUrl
     */
    public function __construct(ClientInterface $httpClient, UuidGeneratorInterface $uuidGenerator, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->uuidGenerator = $uuidGenerator;
        $this->baseUrl = $baseUrl;
    }


    /**
     * @param ViewRequest $viewRequest
     * @return ViewResponse
     * @throws RequestException
     */
    public function view(ViewRequest $viewRequest): ViewResponse
    {
        try {

            $query = [];
            if (!$viewRequest->getParameters()->isEmpty()) {
                $query['parameters'] = $viewRequest->getParameters();
            }
            if (!$viewRequest->getPagiantion()->isEmpty()) {
                $query['pagination'] = $viewRequest->getPagiantion();
            }
            if ($viewRequest->getParameters() !== null) {
                $query['orderBy'] = $viewRequest->getOrderBy();
            }

            $requestUri = new Uri();
            $requestUri->withHost($this->baseUrl);
            $requestUri->withPath('/cv/'.$viewRequest->getName());
            $requestUri->withQuery(http_build_query($query));

            $request = $this->generateRequest(null, 'GET', $requestUri);
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() === 404) {
                throw new NotFoundException('View not found');
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            $viewResponse = new ViewResponse(
                new KeyValueCollection(\array_key_exists('parameters', $responseData) ? $responseData['parameters'] : []),
                new KeyValueCollection(\array_key_exists('pagination', $responseData) ? $responseData['pagination'] : []),
                $parameters['orderBy'] ?? null,
                new KeyValueCollection(\array_key_exists('data', $responseData) ? $responseData['data'] : [])
            );

            return $viewResponse;

        } catch (\Throwable $exception) {
            throw new RequestException('An Error occurred while performing this request');
        }
    }

    /**
     * @param CommandRequest $commandRequest
     * @return CommandRequest
     * @throws RequestException
     */
    public function execute(CommandRequest $commandRequest): CommandRequest
    {
        try {
            $body = [
                $this->uuidGenerator->generate() => [
                    'command' => $commandRequest->getCommand(),
                    'parameters' => $commandRequest->getParameters()->all(),
                ],
            ];

            return $this->handleCommandRequest($body)[0];

        } catch (\Throwable $exception) {
            throw new RequestException('An Error occurred while performing this request');
        }

    }

    /**
     * @param CommandRequest[] $commandRequests
     * @return CommandResponse[]
     * @throws RequestException
     */
    public function executeMultiple(array $commandRequests): array
    {
        try {
            $body = [];
            foreach ($commandRequests as $commandRequest) {
                $body[$this->uuidGenerator->generate()] = [
                    'command' => $commandRequest->getCommand(),
                    'parameters' => $commandRequest->getParameters()->all(),
                ];
            }

            return $this->handleCommandRequest($body);

        } catch (\Throwable $exception) {
            throw new RequestException('An Error occurred while performing this request');
        }

    }

    /**
     * @param array|null $content
     * @param string $method
     * @param UriInterface $requestUri
     * @return RequestInterface
     */
    private function generateRequest(?array $content, string $method, UriInterface $requestUri): RequestInterface
    {
        return new Request(
            $method,
            $requestUri,
            ['Content-Type' => 'application/json'],
            json_encode($content) ?? null
        );
    }

    /**
     * @param array $body
     * @return CommandResponse[]
     * @throws \Exception
     */
    private function handleCommandRequest(array $body): array
    {
        $requestUri = new Uri();
        $requestUri->withHost($this->baseUrl);
        $requestUri->withPath('/cv/execute');

        $request = $this->generateRequest($body, 'POST', $requestUri);

        $response = $this->httpClient->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);
        $commandResponse = [];

        foreach ((array)$responseData as $id => $response) {
            $commandResponse[] = new CommandResponse(
                $responseData['status'],
                new AbstractCollection(\array_key_exists('result', $responseData) ? $responseData['result'] : [])
            );
        }

        return $commandResponse;
    }
}
