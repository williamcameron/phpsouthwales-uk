<?php

namespace Drupal\event_pull\Service\Importer;

use Drupal\advancedqueue\Entity\Queue;
use Drupal\advancedqueue\Job;
use Drupal\event_pull\ValueObject\Event;
use Drupal\event_pull\Service\EventLoader\EventLoaderInterface;

/**
 * A service for importing events.
 */
class EventImporter {

  /**
   * The event loader service.
   *
   * @var \Drupal\event_pull\Service\EventLoader\EventLoaderInterface
   */
  private EventLoaderInterface $eventLoader;

  /**
   * EventImporter constructor.
   *
   * @param \Drupal\event_pull\Service\EventLoader\EventLoaderInterface $eventLoader
   *   The event loader service.
   */
  public function __construct(
      EventLoaderInterface $eventLoader
  ) {
    $this->eventLoader = $eventLoader;
  }

  /**
   * Import events.
   */
  public function import(): void {
    $queue = Queue::load('event_pull');
    $this->eventLoader->getUpcoming()
      ->each(function (Event $event) use ($queue): void {
        $job = Job::create('event_pull_create_update_node', $event->toArray());
        $queue->enqueueJob($job);
      });
  }

}
