<?php

namespace Drupal\Tests\event_pull\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;

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
  }

  /**
   * Test that event nodes are created from pulled events.
   */
  public function testEventNodesAreCreated() {
    // Given that there is an event.

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

}