<?php
declare(strict_types=1);

namespace Eos\ComView\Client\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo instead of an own client interface which only makes use of PSR-7 request/response, we should use a PSR-18 http client in our library
 */
interface ClientInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;

}
