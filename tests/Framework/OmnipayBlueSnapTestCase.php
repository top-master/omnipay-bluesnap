<?php

namespace Omnipay\BlueSnap\Test\Framework;

use InvalidArgumentException;
use Omnipay\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionObject;

/**
 * Class OmnipayBlueSnapTestCase
 *
 * @method \Omnipay\Common\Http\ClientInterface getHttpClient()
 * @method \Symfony\Component\HttpFoundation\Request getHttpRequest()
 */
class OmnipayBlueSnapTestCase extends TestCase
{

  /**
   * Get a mock response for a client by mock file name. Overrides the Omnipay
   * default to add support for substitutions, as described in setMockHttpResponse.
   *
   * @param string $path
   * @param array<string, string> $substitutions
   * @return ResponseInterface
   */
  public function getMockHttpResponse($path, $substitutions = array())
  {
    if ($path instanceof ResponseInterface) {
      return $path;
    }

    $ref = new ReflectionObject($this);
    $dir = dirname($ref->getFileName() ?: '');

    $fullPath = $dir . '/Mock/' . $path;
    // if mock file doesn't exist, check parent directory
    if (!file_exists($fullPath) && file_exists($dir . '/../Mock/' . $path)) {
      $fullPath = $dir . '/../Mock/' . $path;
    }
    if (!file_exists($fullPath)) {
      throw new InvalidArgumentException('Unable to open mock file: ' . $fullPath);
    }

    $fileContents = file_get_contents($fullPath) ?: '';
    foreach ($substitutions as $search => $replace) {
      $fileContents = str_replace('[' . $search . ']', $replace, $fileContents);
    }
    return \GuzzleHttp\Psr7\Message::parseResponse($fileContents);
  }

  /**
   * Set a mock response from a mock file on the next client request.
   *
   * This method assumes that mock response files are located under the
   * tests/Mock/ subdirectory. A mock response is added to the next
   * request sent by the client.
   *
   * An array of path can be provided and the next x number of client requests are
   * mocked in the order of the array where x = the array length.
   *
   * This is an override of the default Omnipay function that adds support for
   * setting an array of substitutions that can be used for randomizing test data.
   * For example, if $substitutions is array('NAME' => 'Fake Name'),
   * then the function will replace all instances of '[NAME]' in the
   * response with 'Fake Name'. Substitutions are not required.
   *
   * @param array<string, string>|string $paths
   * @param array<string, string> $substitutions
   *
   * @return void
   */
  public function setMockHttpResponse($paths, $substitutions = array())
  {
    $mock = $this->getMockClient();
    foreach ((array)$paths as $path) {
      $mock->addResponse($this->getMockHttpResponse($path, $substitutions));
    }
  }

  /**
   * @return \PHPUnit\Framework\MockObject\MockObject
   */
  public function getMock($originalClassName, $methods = [], array $ctorArguments = []) {
    $mockObject = $this->getMockBuilder($originalClassName)
      ->setMethods($methods)
      ->setConstructorArgs($ctorArguments)
      ->getMock();
    $this->registerMockObject($mockObject);
    return $mockObject;
  }
}
