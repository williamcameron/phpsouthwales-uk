<?php

namespace Drupal\Tests\event_pull\Kernel;

use Drupal\advancedqueue\Entity\Queue;
use Drupal\advancedqueue\Job;
use Drupal\event_pull\Service\EventLoader\EventLoaderInterface;
use Drupal\event_pull\Service\EventLoader\MeetupEventLoader;
use Drupal\event_pull\Service\Importer\EventImporter;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\Tests\event_pull\Traits\MockHttpClientTrait;

/**
 * Test that nodes are created from pulled events.
 */
class CreateEventsTest extends EntityKernelTestBase {

  use MockHttpClientTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'advancedqueue',
    'event_pull',
    'node',
    'taxonomy',
  ];

  /**
   * Node storage handler.
   *
   * @var \Drupal\node\NodeStorage
   */
  private $nodeStorage;

  /**
   * Taxonomy term storage handler.
   *
   * @var \Drupal\taxonomy\TermStorage
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installConfig(self::$modules);

    $this->installSchema('advancedqueue', ['advancedqueue']);

    $this->container->setAlias(EventLoaderInterface::class, MeetupEventLoader::class);

    $entityTypeManager = $this->container->get('entity_type.manager');

    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->termStorage = $entityTypeManager->getStorage('taxonomy_term');

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
   * Test that event nodes are created from pulled events.
   *
   * @group event-import
   * @group event-import-meetup
   */
  public function testEventNodesAreCreated() {
    /** @var \Drupal\event_pull\Service\Importer\EventImporter $eventImporter */
    $eventImporter = $this->container->get(EventImporter::class);
    $eventImporter->import();

    // Then the item should be queued.
    $queue = Queue::load('default');
    /** @var \Drupal\advancedqueue\Plugin\AdvancedQueue\Backend\BackendBase $backend */
    $backend = $queue->getBackend();
    $jobs = $backend->countJobs();
    $this->assertEqual(1, $jobs[Job::STATE_QUEUED]);

    /** @var \Drupal\advancedqueue\ProcessorInterface $processor */
    $processor = $this->container->get('advancedqueue.processor');
    $processor->processQueue($queue);

    // When the queue is processed, the corresponding event node should be
    // created.
    $event = $this->nodeStorage->load(1);
    $this->assertInstanceOf(NodeInterface::class, $event);
    $this->assertSame('Practical Static Analysis', $event->label());

    // The taxonomy term for the venue should also be created.
    $venue = $this->termStorage->load(1);
    $this->assertInstanceOf(TermInterface::class, $venue);
  }

}
