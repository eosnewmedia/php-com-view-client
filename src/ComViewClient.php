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
    //@todo in composer.json: library should not depend on ramsey/uuid or guzzlehttp/psr7, move ramsey to require-dev and suggest and replace guzzle with psr/http-client and psr/http-factory
    //@todo please write a readme file how to use this library

    /**
     * @var ClientInterface
     * @todo instead of an own client interface which only makes use of PSR-7 request/response, we should use a PSR-18 http client in our library
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

            $requestUri = new Uri(); //@todo don't let our library depend on concrete external classes, instead create the uri via an instance of UriFactoryInterface from psr-17
            $requestUri->withHost($this->baseUrl);
            // @todo because a base uri can look like "example.com/api" you can not overwrite the path. extend the path like "basePath+cvPath"
            $requestUri->withPath('/cv/'.$viewRequest->getName());
            $requestUri->withQuery(http_build_query($query));

            $request = $this->generateRequest(null, 'GET', $requestUri);
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() === 404) {
                //@todo don't throw an extension here, but give the status (as code and as constant like FAILURE) back to the application
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
                //@todo the id should be given in command request, because otherwise reassigning responses to requests is impossible in the application; possibility: create a "CommandRequestFactory"-Method here where id's are assigned
                $this->uuidGenerator->generate() => [
                    'command' => $commandRequest->getCommand(),
                    'parameters' => $commandRequest->getParameters()->all(),
                ],
            ];

            return $this->handleCommandRequest($body)[0]; // @todo this can fail if for some reason no response is given

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
                //@todo the id should be given in command request, because otherwise reassigning responses to requests is impossible in the application; possibility: create a "CommandRequestFactory"-Method here where id's are assigned
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
        //@todo don't let our library depend on concrete external classes, instead create the request via an instance of RequestFactoryInterface from psr-17
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
            // @todo check for array key exist before usage
            //@todo if you don't use the id in the command response, for multiple commands it will be impossible to assign response to request
            $commandResponse[] = new CommandResponse(
                $responseData['status'],
                new AbstractCollection(\array_key_exists('result', $responseData) ? $responseData['result'] : [])
            );
        }

        return $commandResponse;
    }
}
