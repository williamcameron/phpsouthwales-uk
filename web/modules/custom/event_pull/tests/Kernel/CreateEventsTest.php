<?php

namespace Drupal\Tests\event_pull\Kernel;

use Drupal\event_pull\Model\Event;
use Drupal\event_pull\Service\EventLoader\MeetupEventLoader;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tightenco\Collect\Support\Collection;

class CreateEventsTest extends EntityKernelTestBase {

  /**
   * @var \Drupal\taxonomy\TermStorage
   */
  private $termStorage;

  /**
   * @var \Drupal\node\NodeStorage
   */
  private $nodeStorage;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'event_pull',
    'node',
    'taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $entityTypeManager = $this->container->get('entity_type.manager');

    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->termStorage = $entityTypeManager->getStorage('taxonomy_term');

    $this->mockHttpClient();
  }

  /**
   * Test that event nodes are created from pulled events.
   */
  public function testEventNodesAreCreated() {
    $this->markTestIncomplete();

    /** @var \Drupal\event_pull\Service\EventLoader\MeetupEventLoader $meetupEventLoader */
    $meetupEventLoader = $this->container->get(MeetupEventLoader::class);
    $events = $meetupEventLoader->getUpcoming();

    // Given that there is an event.
    $this->assertInstanceOf(Collection::class, $events);
    $this->assertCount(1, $events);
    $event = $events->first();
    $this->assertInstanceOf(Event::class, $event);

    // When I pull it in.
    // Then the item should be queued.

    // When the queue is processed, the corresponding event node should be
    // created.
    $event = $this->nodeStorage->load(1);
    $this->assertInstanceOf(NodeInterface::class, $event);
    $this->assertSame('Practical Static Analysis', $event->label());

    // The taxonomy term for the venue should also be created.
    $venue = $this->termStorage->load(1);
    $this->assertInstanceOf(TermInterface::class, $venue);
  }

  /**
   * Replace the http_client service with a mocked version.
   */
  private function mockHttpClient(): void {
    $data = [
      (object) [
        'title' => 'Practical Static Analysis',
        'status' => 'past',
        'time' => '1556643600000',
        'yes_rsvp_count' => 17,
        'venue' => [
          'id' => 26204581,
          'name' => 'Stadium Plaza',
          'lat' => 51.47688293457031,
          'lon' => -3.181555986404419,
          'repinned' => false,
          'address_1' => 'Wood St',
          'city' => 'Cardiff',
          'country' => 'gb',
          'localized_country_name' => 'United Kingdom'
        ],
        'link' => 'https://www.meetup.com/PHP-South-Wales/events/260287298/',
        'description' => '<p>This month we have the pleasure of PHP South West organiser David Liddament coming this side of the bridge to give us an exciting talk all about Practical Static Analysis.</p>',
      ],
    ];

    $mock = new MockHandler([
      new Response(200, [], json_encode($data)),
    ]);

    // Replace the existing http_client service with a mock Client.
    $this->container->set('http_client', new Client([
      'handler' => HandlerStack::create($mock)
    ]));
  }

}
