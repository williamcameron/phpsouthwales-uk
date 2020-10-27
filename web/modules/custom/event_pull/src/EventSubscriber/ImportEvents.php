<?php

namespace Drupal\event_pull\EventSubscriber;

use Drupal\event_pull\Service\Importer\EventImporter;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Imports events on cron runs.
 */
class ImportEvents implements EventSubscriberInterface {

  /**
   * The event importer service.
   *
   * @var \Drupal\event_pull\Service\Importer\EventImporter
   */
  private EventImporter $eventImporter;

  /**
   * ImportEvents constructor.
   */
  public function __construct(EventImporter $eventImporter) {
    $this->eventImporter = $eventImporter;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      HookEventDispatcherInterface::CRON => 'importEvents',
    ];
  }

  /**
   * Import events.
   */
  public function importEvents(): void {
    $this->eventImporter->import();
  }

}
