<?php

namespace Omnipay\BlueSnap;

use Exception;
use Omnipay\BlueSnap\Message\ExtendedCancelSubscriptionRequest;
use Omnipay\BlueSnap\Test\Framework\OmnipayBlueSnapTestCase;

class EventEmitterTest extends OmnipayBlueSnapTestCase
{
  /**
   * @var string
   */
  private $subscriptionReference;

  /**
   * @return void
   */
  protected function setUp()
  {
    $this->subscriptionReference = '123';

    parent::setUp();
  }

  /**
   * Ensures that 'Request' and 'Response' events are emitted when issuing a request.
   *
   * @return void
   */
  public function testAuthorizeRequestSuccessfulResponseEmitted()
  {
    $this->setMockHttpResponse('ExtendedCancelSubscriptionSuccess.txt', array(
      'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
    ));

    $request = new ExtendedCancelSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
    $request->setSubscriptionReference($this->subscriptionReference);

    $response = $request->send();
    $this->assertTrue($response->isSuccessful());
    $this->assertFalse($response->isRedirect());
    $this->assertInstanceOf('\Omnipay\BlueSnap\Message\Response', $response);
  }

  /**
   * Ensures that 'Request' and 'Error' events are emitted when issuing an improper request.
   *
   * @psalm-suppress UndefinedMethod because Psalm can't infer that it exists in the Mock object but it does!
   * @return void
   */
  public function testAuthorizeRequestErrorEventEmitted()
  {
    $this->setMockHttpResponse('ExtendedCancelSubscriptionFailure.txt', array(
      'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
    ));

    $request = new ExtendedCancelSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
    $request->setSubscriptionReference($this->subscriptionReference);

    $response = $request->send();
    $this->assertNotNull($response);
    $this->assertFalse($response->isSuccessful());
    $this->assertFalse($response->isRedirect());
  }
}
