<?php

use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Psr7\StreamWrapper;
use Psr\Http\Message\ResponseInterface;

class ResponseWrapper implements ResponseInterface
{
  /** @var Guzzle\Http\Message\Response */
  protected $response;

  public function __construct(Response $response)
  {
    $this->response = $response;
  }

  protected function copy()
  {
    $clone = new Response($this->response->getStatusCode(), $this->response->getHeaders(), $this->response->getBody());
    $clone->setProtocol($this->response->getProtocol(), $this->response->getProtocolVersion());
    $clone->setStatus($this->response->getStatusCode(), $this->response->getReasonPhrase());
    $clone->setEffectiveUrl($this->response->getEffectiveUrl());
    $clone->setInfo($this->response->getInfo());
    return new ResponseWrapper(
      $clone
    );
  }

  /**
   * Retrieves the HTTP protocol version as a string.
   *
   * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
   *
   * @return string HTTP protocol version.
   */
  public function getProtocolVersion()
  {
    return $this->response->getProtocolVersion();
  }

  /**
   * Return an instance with the specified HTTP protocol version.
   *
   * The version string MUST contain only the HTTP version number (e.g.,
   * "1.1", "1.0").
   *
   * This method MUST be implemented in such a way as to retain the
   * immutability of the message, and MUST return an instance that has the
   * new protocol version.
   *
   * @param string $version HTTP protocol version
   * @return static
   */
  public function withProtocolVersion($version)
  {
    $clone = $this->copy();
    $clone->response->setProtocol('HTTP', $version);
    return $clone;
  }

  /**
   * Retrieves all message header values.
   *
   * The keys represent the header name as it will be sent over the wire, and
   * each value is an array of strings associated with the header.
   *
   *     // Represent the headers as a string
   *     foreach ($message->getHeaders() as $name => $values) {
   *         echo $name . ": " . implode(", ", $values);
   *     }
   *
   *     // Emit headers iteratively:
   *     foreach ($message->getHeaders() as $name => $values) {
   *         foreach ($values as $value) {
   *             header(sprintf('%s: %s', $name, $value), false);
   *         }
   *     }
   *
   * While header names are not case-sensitive, getHeaders() will preserve the
   * exact case in which headers were originally specified.
   *
   * @return string[][] Returns an associative array of the message's headers. Each
   *     key MUST be a header name, and each value MUST be an array of strings
   *     for that header.
   */
  public function getHeaders()
  {
    return $this->response->getHeaders();
  }

  /**
   * Checks if a header exists by the given case-insensitive name.
   *
   * @param string $name Case-insensitive header field name.
   * @return bool Returns true if any header names match the given header
   *     name using a case-insensitive string comparison. Returns false if
   *     no matching header name is found in the message.
   */
  public function hasHeader($name)
  {
    return $this->response->hasHeader($name);
  }

  /**
   * Retrieves a message header value by the given case-insensitive name.
   *
   * This method returns an array of all the header values of the given
   * case-insensitive header name.
   *
   * If the header does not appear in the message, this method MUST return an
   * empty array.
   *
   * @param string $name Case-insensitive header field name.
   * @return string[] An array of string values as provided for the given
   *    header. If the header does not appear in the message, this method MUST
   *    return an empty array.
   */
  public function getHeader($name)
  {
    return $this->response->getHeader($name)->toArray() ?? [];
  }

  /**
   * Retrieves a comma-separated string of the values for a single header.
   *
   * This method returns all of the header values of the given
   * case-insensitive header name as a string concatenated together using
   * a comma.
   *
   * NOTE: Not all header values may be appropriately represented using
   * comma concatenation. For such headers, use getHeader() instead
   * and supply your own delimiter when concatenating.
   *
   * If the header does not appear in the message, this method MUST return
   * an empty string.
   *
   * @param string $name Case-insensitive header field name.
   * @return string A string of values as provided for the given header
   *    concatenated together using a comma. If the header does not appear in
   *    the message, this method MUST return an empty string.
   */
  public function getHeaderLine($name)
  {
    return join(',', $this->response->getHeader($name)->toArray());
  }

  /**
   * Return an instance with the provided value replacing the specified header.
   *
   * While header names are case-insensitive, the casing of the header will
   * be preserved by this function, and returned from getHeaders().
   *
   * This method MUST be implemented in such a way as to retain the
   * immutability of the message, and MUST return an instance that has the
   * new and/or updated header and value.
   *
   * @param string $name Case-insensitive header field name.
   * @param string|string[] $value Header value(s).
   * @return static
   * @throws \InvalidArgumentException for invalid header names or values.
   */
  public function withHeader($name, $value)
  {
    $clone = $this->copy();
    $clone->response->setHeader($name, $value);
    return $clone;
  }

  /**
   * Return an instance with the specified header appended with the given value.
   *
   * Existing values for the specified header will be maintained. The new
   * value(s) will be appended to the existing list. If the header did not
   * exist previously, it will be added.
   *
   * This method MUST be implemented in such a way as to retain the
   * immutability of the message, and MUST return an instance that has the
   * new header and/or value.
   *
   * @param string $name Case-insensitive header field name to add.
   * @param string|string[] $value Header value(s).
   * @return static
   * @throws \InvalidArgumentException for invalid header names or values.
   */
  public function withAddedHeader($name, $value)
  {
    $clone = $this->copy();
    $clone->response->addHeader($name, $value);
    return $clone;
  }

  /**
   * Return an instance without the specified header.
   *
   * Header resolution MUST be done without case-sensitivity.
   *
   * This method MUST be implemented in such a way as to retain the
   * immutability of the message, and MUST return an instance that removes
   * the named header.
   *
   * @param string $name Case-insensitive header field name to remove.
   * @return static
   */
  public function withoutHeader($name)
  {
    $clone = $this->copy();
    $clone->response->removeHeader($name);
    return $clone;
  }

  /**
   * Gets the body of the message.
   *
   * @return \Psr\Http\Message\StreamInterface Returns the body as a stream.
   */
  public function getBody()
  {
    /** @noinspection PhpIncompatibleReturnTypeInspection */
    return $this->response->getBody(false);
  }

  /**
   * Return an instance with the specified message body.
   *
   * The body MUST be a StreamInterface object.
   *
   * This method MUST be implemented in such a way as to retain the
   * immutability of the message, and MUST return a new instance that has the
   * new body stream.
   *
   * @param \Psr\Http\Message\StreamInterface $body Body.
   * @return static
   * @throws \InvalidArgumentException When the body is not valid.
   */
  public function withBody(\Psr\Http\Message\StreamInterface $body)
  {
    $clone = $this->copy();
    $clone->response->setBody(new EntityBody(
      StreamWrapper::getResource($body)
    ));
    return $clone;
  }

  /**
   * Gets the response status code.
   *
   * The status code is a 3-digit integer result code of the server's attempt
   * to understand and satisfy the request.
   *
   * @return int Status code.
   */
  public function getStatusCode()
  {
    return $this->response->getStatusCode();
  }

  /**
   * Return an instance with the specified status code and, optionally, reason phrase.
   *
   * If no reason phrase is specified, implementations MAY choose to default
   * to the RFC 7231 or IANA recommended reason phrase for the response's
   * status code.
   *
   * This method MUST be implemented in such a way as to retain the
   * immutability of the message, and MUST return an instance that has the
   * updated status and reason phrase.
   *
   * @link http://tools.ietf.org/html/rfc7231#section-6
   * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
   * @param int $code The 3-digit integer result code to set.
   * @param string $reasonPhrase The reason phrase to use with the
   *     provided status code; if none is provided, implementations MAY
   *     use the defaults as suggested in the HTTP specification.
   * @return static
   * @throws \InvalidArgumentException For invalid status code arguments.
   */
  public function withStatus($code, $reasonPhrase = '')
  {
    $clone = $this->copy();
    $clone->response->setStatus($code, $reasonPhrase);
    return $clone;
  }

  /**
   * Gets the response reason phrase associated with the status code.
   *
   * Because a reason phrase is not a required element in a response
   * status line, the reason phrase value MAY be null. Implementations MAY
   * choose to return the default RFC 7231 recommended reason phrase (or those
   * listed in the IANA HTTP Status Code Registry) for the response's
   * status code.
   *
   * @link http://tools.ietf.org/html/rfc7231#section-6
   * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
   * @return string Reason phrase; must return an empty string if none present.
   */
  public function getReasonPhrase()
  {
    return $this->response->getReasonPhrase();
  }
}
