# eos/php-com-view-client
PHP client implementation for ComView-API. Designed as client used with [eos/php-com-view-server](https://github.com/eosnewmedia/php-com-view-server).

# Installation

Install this library via composer:

    composer require eos/php-com-view-client
    
 # Configuration
 
 This assumes you have implemented the [PSR 17](https://www.php-fig.org/psr/psr-17) and [PSR 18](https://www.php-fig.org/psr/psr-18) Interfaces that are passed as dependencies:
 
    Psr\Http\Client\ClientInterface;
    Psr\Http\Message\RequestFactoryInterface;
    Psr\Http\Message\StreamFactoryInterface;
    Psr\Http\Message\UriFactoryInterface;
 
 Create a new instance of `Eos\ComView\Client\ComViewClient`. This will be the entrypoint for the application.
 
 ```php
$client = new Eos\ComView\Client\ComViewClient(
                $psrHttpClient,
                $baseUrl, 
                $psrUriFactory, 
                $psrRequestFactory, 
                $psrStreamFactory
            );
```

# Usage

This library provides 3 methods to send view- and command-requests.

### Eos\ComView\Client\ComViewClient::view($viewRequest)

 `Eos\ComView\Client\ComViewClient::view($viewRequest)` expects an instance of `Eos\ComView\Client\Value\ViewRequest` and returns an object of `Eos\ComView\Client\Value\ViewResponse`. 
 
 ```php
$viewRequest = new Eos\ComView\Client\Value\ViewRequest(
    $viewName,      //string
    $parameters,    //array
    $pagination,    //array
    $orderBy        //string|null
);
$response = $client->view($viewRequest);
```

### Eos\ComView\Client\ComViewClient::execute($id, $commandRequest)

 `Eos\ComView\Client\ComViewClient::execute($id, $commandRequest)` expects a string ID and an instance of `Eos\ComView\Client\Value\CommandRequest` and returns an object of `Eos\ComView\Client\Value\CommandResponse` or `null`.
 
  
  ```php
 $commandRequest = new Eos\ComView\Client\Value\CommandRequest(
     $commandName,  //string
     $parameters    //array
 );
 $id = '123';
 $response = $client->execute($id, $commandRequest);
 ```
 
 ### Eos\ComView\Client\ComViewClient::executeMultiple($commandRequests)
 
 `Eos\ComView\Client\ComViewClient::executeMultiple($commandRequests)` expects an array of instances of `Eos\ComView\Client\Value\CommandRequest`  with a unique ID an key and returns an array of object of `Eos\ComView\Client\Value\CommandResponse`.
 
 

  ```php
  $commandRequests = [
        '123'=> new Eos\ComView\Client\Value\CommandRequest(/*...*/),
        '234'=> new Eos\ComView\Client\Value\CommandRequest(/*...*/),
        '345'=> new Eos\ComView\Client\Value\CommandRequest(/*...*/)
  ];
 $response = $client->executeMultiple($commandRequests);
 ```
 
 

