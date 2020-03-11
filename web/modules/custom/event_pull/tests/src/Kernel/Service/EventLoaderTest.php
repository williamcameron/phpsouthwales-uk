<?php

namespace Drupal\Tests\event_pull\Kernel\Service;

use Drupal\event_pull\ValueObject\Event;
use Drupal\event_pull\Service\EventLoader\EventLoaderInterface;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\Tests\event_pull\Traits\MockHttpClientTrait;
use Tightenco\Collect\Support\Collection;

/**
 * Test loading events from an external source.
 */
class EventLoaderTest extends EntityKernelTestBase {

  use MockHttpClientTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'event_pull',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $mockData = [
      (object) [
        'name' => 'Practical Static Analysis',
        'status' => 'past',
        'time' => '1556643600000',
        'yes_rsvp_count' => 17,
        'venue' => [
          'id' => 26204581,
          'name' => 'Stadium Plaza',
          'lat' => 51.47688293457031,
          'lon' => -3.181555986404419,
          'repinned' => FALSE,
          'address_1' => 'Wood St',
          'city' => 'Cardiff',
          'country' => 'gb',
          'localized_country_name' => 'United Kingdom',
        ],
        'link' => 'https://www.meetup.com/PHP-South-Wales/events/260287298/',
        'description' => '<p>This month we have the pleasure of PHP South West organiser David Liddament coming this side of the bridge to give us an exciting talk all about Practical Static Analysis.</p>',
      ],
    ];
    $this->mockHttpClient($mockData);
  }

  /**
   * Test loading events.
   */
  public function testLoadingEvents() {
    /** @var \Drupal\event_pull\Service\EventLoader\MeetupEventLoader $meetupEventLoader */
    $meetupEventLoader = $this->container->get(EventLoaderInterface::class);
    $events = $meetupEventLoader->getUpcoming();

    $this->assertInstanceOf(Collection::class, $events);
    $this->assertCount(1, $events);
    $event = $events->first();
    $this->assertInstanceOf(Event::class, $event);
    $this->assertSame('Practical Static Analysis', $event->getName());
  }

}
