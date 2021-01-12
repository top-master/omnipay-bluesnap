<?php

namespace Omnipay\BlueSnap\Test\Framework;

use Guzzle\Http\Client;
use Http\Mock\Client as MockClient;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class TestHttpClient extends Client
  implements ClientInterface
{

  public function __construct(MockClient $mock, $config = null)
  {
    parent::__construct('', array_merge([
      'redirect.disable' => true,
    ], $config));
  }

  /**
   * Creates a new PSR-7 request.
   *
   * @param string $method
   * @param string|UriInterface $uri
   * @param array $headers
   * @param resource|string|StreamInterface|null $body
   * @param string $protocolVersion
   *
   * @return ResponseInterface
   * @throws NetworkException if there is an error with the network or the remote server cannot be reached.
   *
   * @throws RequestException when the HTTP client is passed a request that is invalid and cannot be sent.
   */
  public function request(string $method, $uri, array $headers = [], $body = null, string $protocolVersion = '1.1'): ResponseInterface
  {
    $r = $this->createRequest($method, $uri, $headers, $body)
      ->setProtocolVersion($protocolVersion)
      ->send();
    return new \ResponseWrapper($r);
  }
}
