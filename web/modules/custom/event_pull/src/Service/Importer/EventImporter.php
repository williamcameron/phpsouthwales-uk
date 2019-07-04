<?php

namespace Drupal\event_pull\Service\Importer;

use Drupal\advancedqueue\Entity\Queue;
use Drupal\advancedqueue\Job;
use Drupal\event_pull\Model\Event;
use Drupal\event_pull\Service\EventLoader\EventLoaderInterface;

class EventImporter {

  private $eventLoader;

  public function __construct(
      EventLoaderInterface $eventLoader
  ) {
    $this->eventLoader = $eventLoader;
  }

  public function import(): void {
    $this->eventLoader->getUpcoming()
      ->each(function (Event $event): void {
        $job = Job::create('event_pull_pulled_event', ['event' => $event]);
        $queue = Queue::load('default');
        $queue->enqueueJob($job);
      });
  }

}
