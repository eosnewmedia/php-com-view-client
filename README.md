# eos/php-com-view-client
PHP client implementation for ComView-API. Designed as client used with [eos/php-com-view-server](https://github.com/eosnewmedia/php-com-view-server).

# Installation

Install this library via composer:

    composer require eos/php-com-view-client
    
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

This library provides 3 methods to send view- and command-requests.

### Eos\ComView\Client\ComViewClient::requestView($viewRequest)

 `Eos\ComView\Client\ComViewClient::requestView($viewRequest)` expects an instance of `Eos\ComView\Client\Value\ViewRequest` and returns an object of `Eos\ComView\Client\Value\ViewResponse`. 
 
 ```php
$viewRequest = new Eos\ComView\Client\Value\ViewRequest(
    $viewName,      //string
    $parameters,    //array
    $pagination,    //array
    $orderBy        //string|null
);
$response = $client->requestView($viewRequest);
```

### Eos\ComView\Client\ComViewClient::executeCommand($commandRequest)

 `Eos\ComView\Client\ComViewClient::executeCommand($commandRequest)` expects an instance of `Eos\ComView\Client\Value\CommandRequest` and returns an instance of `Eos\ComView\Client\Value\CommandResponse`.
 
  
  ```php
 $commandRequest = new Eos\ComView\Client\Value\CommandRequest(
     $commandName,  //string
     $parameters    //array
 );
 $response = $client->executeCommand($commandRequest);
 ```
 
 ### Eos\ComView\Client\ComViewClient::executeCommands($commandRequests)
 
 `Eos\ComView\Client\ComViewClient::executeCommands($commandRequests)` expects an array of instances of `Eos\ComView\Client\Value\CommandRequest`  with a unique ID (for this request) as key and returns an array of instances of `Eos\ComView\Client\Value\CommandResponse` with the unique ids as keys.
 
 

  ```php
  $commandRequests = [
        '1'=> new Eos\ComView\Client\Value\CommandRequest(/*...*/),
        '2'=> new Eos\ComView\Client\Value\CommandRequest(/*...*/),
        '3'=> new Eos\ComView\Client\Value\CommandRequest(/*...*/)
  ];
 $response = $client->executeCommands($commandRequests);
 ```
 