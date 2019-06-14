<?php

namespace Drupal\event_pull\Model;

/**
 * A value object for working with events returned from external sources.
 */
class Event {

  /**
   * The original event data.
   *
   * @var \stdClass
   */
  private $eventData;

  /**
   * Event constructor.
   *
   * @param \stdClass $eventData
   *   The event data.
   */
  public function __construct(\stdClass $eventData) {
    $this->eventData = $eventData;
  }

  /**
   * The name of the event.
   *
   * @return string
   */
  public function getName(): string {
    return $this->eventData->name;
  }

  public function getDate(): int {
    return substr($this->eventData->time, 0, 10);
  }

}
