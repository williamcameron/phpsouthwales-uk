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
    // Core.
    'node',
    'taxonomy',

    // Contrib.
    'advancedqueue',

    // Custom.
    'phpsw_event',
    'event_pull',
  ];

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;

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
   * The event importer service.
   *
   * @var \Drupal\event_pull\Service\Importer\EventImporter
   */
  private $eventImporter;

  /**
   * The queue.
   *
   * @var \Drupal\advancedqueue\Entity\Queue
   */
  private $queue;

  /**
   * The queue processor.
   *
   * @var \Drupal\advancedqueue\ProcessorInterface
   */
  private $processor;

  /**
   * The queue backend.
   *
   * @var \Drupal\advancedqueue\Plugin\AdvancedQueue\Backend\BackendInterface
   */
  private $backend;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $mockData = [
      (object) [
        'name' => 'Practical Static Analysis',
        'time' => '1556643600000',
        'venue' => [
          'name' => 'Stadium Plaza',
        ],
      ],
      (object) [
        'name' => 'How to do more with PHPCS',
        'time' => '1559237400000',
        'venue' => [
          'name' => 'Stadium Plaza',
        ],
      ],
    ];
    $this->mockHttpClient($mockData);

    $this->installConfig(self::$modules);

    $this->installEntitySchema('taxonomy_term');
    $this->installSchema('advancedqueue', ['advancedqueue']);

    $this->container->setAlias(EventLoaderInterface::class, MeetupEventLoader::class);

    $entityTypeManager = $this->container->get('entity_type.manager');
    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->termStorage = $entityTypeManager->getStorage('taxonomy_term');

    $this->eventImporter = $this->container->get(EventImporter::class);
    $this->queue = Queue::load('event_pull');
    $this->backend = $this->queue->getBackend();
    $this->processor = $this->container->get('advancedqueue.processor');
  }

  /**
   * Test that event nodes are created from pulled events.
   *
   * @group event-import
   * @group event-import-meetup
   * @group event-import-meetup-event
   */
  public function testEventNodesAreCreated() {
    $this->eventImporter->import();

    $jobs = $this->backend->countJobs();
    $this->assertEqual(2, $jobs[Job::STATE_QUEUED]);

    $this->processor->processQueue($this->queue);

    $events = collect($this->nodeStorage->loadMultiple())->values();
    $this->assertSame(2, $events->count());

    tap($events->get(0), function (NodeInterface $event) {
      $this->assertSame('How to do more with PHPCS', $event->label());
    });

    tap($events->get(1), function (NodeInterface $event) {
      $this->assertSame('Practical Static Analysis', $event->label());
    });
  }

  /**
   * Test that event nodes are created from pulled events.
   *
   * @group event-import
   * @group event-import-meetup
   * @group event-import-meetup-venue
   */
  public function testEventVenueTermsAreCreated() {
    $this->eventImporter->import();
    $this->processor->processQueue($this->queue);

    $terms = collect($this->termStorage->loadMultiple());
    $this->assertCount(1, $terms, 'There should be a single term per venue');

    /** @var \Drupal\taxonomy\TermInterface $venue */
    $venue = $terms->first();
    $this->assertInstanceOf(TermInterface::class, $venue);
    $this->assertSame('Stadium Plaza', $venue->label());

    $events = collect($this->nodeStorage->loadByProperties([
      'type' => 'event',
    ]))->values();

    tap($events->get(0), function (NodeInterface $event) use ($venue): void {
      $this->assertSame($venue->id(), $event->get('field_venue')->getString());
    });

    tap($events->get(1), function (NodeInterface $event) use ($venue): void {
      $this->assertSame($venue->id(), $event->get('field_venue')->getString());
    });
  }

}
