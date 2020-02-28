<?php

namespace Drupal\Tests\event_pull\Kernel;

use Drupal\advancedqueue\Entity\Queue;
use Drupal\event_pull\Service\EventLoader\EventLoaderInterface;
use Drupal\event_pull\Service\Importer\EventImporter;
use Drupal\event_pull\Service\Repository\EventRepository;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\Tests\event_pull\Traits\MockHttpClientTrait;
use Drupal\event_pull_test\Service\Loader\FakeEventLoader;

/**
 * Test that nodes are created from pulled events.
 */
class UpdatingEventsTest extends EntityKernelTestBase {

  use MockHttpClientTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    // Core.
    'datetime',
    'link',
    'node',
    'taxonomy',

    // Contrib.
    'advancedqueue',

    // Custom.
    'phpsw_event',
    'event_pull',
    'event_pull_test',
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
   *
   * @var \Drupal\event_pull\Service\Repository\EventRepository
   */
  private $eventRepository;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $mockData = [
      (object) [
        'name' => 'Practical Static Analysis',
        'link' => 'https://www.meetup.com/PHP-South-Wales/events/260287298/',
        'description' => "<p>This month we have the pleasure of PHP South West organiser David Liddament coming this side of the bridge to give us an exciting talk all about Practical Static Analysis.</p>",
        'id' => '260287298',
        'created' => '1554234178000',
        'time' => '1556643600000',
        'venue' => [
          'name' => 'Stadium Plaza',
          'id' => '26204581',
        ],
      ],
    ];

    $this->installConfig(self::$modules);

    $this->installEntitySchema('taxonomy_term');
    $this->installSchema('advancedqueue', ['advancedqueue']);
    $this->installSchema('node', ['node_access']);

    $entityTypeManager = $this->container->get('entity_type.manager');
    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->termStorage = $entityTypeManager->getStorage('taxonomy_term');

    $this->eventRepository = $this->container->get(EventRepository::class);
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
  public function testEventNodesAreUpdated() {
    $this->container->setAlias(EventLoaderInterface::class, FakeEventLoader::class);

    $this->eventImporter->import();
    $this->processor->processQueue($this->queue);

    $event = $this->eventRepository->getAll()->first();
    $this->assertInstanceOf(NodeInterface::class, $event);
    $this->assertSame('1', $event->id());
    $this->assertSame('1', $event->getRevisionId());

    $this->eventImporter->import();
    $this->processor->processQueue($this->queue);

    $event = $this->eventRepository->getAll()->first();
    $this->assertInstanceOf(NodeInterface::class, $event);
    $this->assertSame('1', $event->id());
    $this->assertSame('2', $event->getRevisionId());
  }

  public function testVenuesAreUpdated() {
    $this->markTestSkipped();
  }

}
