<?php

namespace Drupal\Tests\event_pull\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * A trait for replacing the http_client service with a mock.
 */
trait MockHttpClientTrait {

  /**
   * Replace the http_client service with a mock, and set response data.
   *
   * @param array $data
   *   The data to set.
   */
  protected function mockHttpClient(array $data = []): void {
    $mock = new MockHandler([
      new Response(200, [], json_encode($data)),
    ]);

    // Replace the existing http_client service with a mock Client.
    $this->container->set('http_client', new Client([
      'handler' => HandlerStack::create($mock),
    ]));
  }

}
