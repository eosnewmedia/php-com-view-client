# eos/php-com-view-client
PHP client implementation for ComView-API.

# Installation

Install this library via composer:

    composer require eos/com-view-client
    
 # Configuration
 
 This assumes you have implemented the [PSR 17](https://www.php-fig.org/psr/psr-17) and [PSR 18](https://www.php-fig.org/psr/psr-18) interfaces that are passed as dependencies:
 
    Psr\Http\Client\ClientInterface;
    Psr\Http\Message\RequestFactoryInterface;
    Psr\Http\Message\StreamFactoryInterface;
    Psr\Http\Message\UriFactoryInterface;
 
 Create a new instance of `Eos\ComView\Client\ComViewClient`. This will be the entry point for the application.
 
 ```php
$client = new Eos\ComView\Client\ComViewClient(
    $baseUrl, 
    $psrHttpClient,
    $psrUriFactory, 
    $psrRequestFactory, 
    $psrStreamFactory
);
```

# Usage

This library provides 2 methods to send view- and command-requests.

### Eos\ComView\Client\ComViewClient::requestView($viewRequest)

 `Eos\ComView\Client\ComViewClient::requestView($viewRequest)` expects an instance of `Eos\ComView\Client\Model\ViewRequest` and returns an instance of `Eos\ComView\Client\Model\ViewResponse`. 
 
 ```php
$viewRequest = new Eos\ComView\Client\Model\ViewRequest(
    $viewName,      //string
    $headers,    //array
    $parameters,    //array
    $pagination,    //array
    $orderBy        //string|null
);
$response = $client->requestView($viewRequest);
```
 
 ### Eos\ComView\Client\ComViewClient::executeCommands($commandRequests)
 
 `Eos\ComView\Client\ComViewClient::executeCommands($commandRequest)` expects an instances of `Eos\ComView\Client\Model\CommandRequest`  and returns an instance of `Eos\ComView\Client\Model\CommandResponse`.
 The command instances in request and response are the same objects, which will be updated during the execution.
 
 
  ```php
  $commandRequest = new Eos\ComView\Client\Model\CommandRequest(
        [
            new Eos\ComView\Client\Model\Command(/*...*/),
            new Eos\ComView\Client\Model\Command(/*...*/),
        ],
        $headers // array
  );
 $response = $client->executeCommands($commandRequest);
 ```
 