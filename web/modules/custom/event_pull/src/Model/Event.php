<?php

namespace Drupal\event_pull\Model;

/**
 * A value object for working with events returned from external sources.
 */
class Event {

  /**
   * The original event data.
   *
   * @var object
   */
  private $eventData;

  /**
   * Event constructor.
   *
   * @var object $eventData
   *   The event data.
   */
  public function __construct(\stdClass $eventData) {
    $this->eventData = $eventData;
  }

  /**
   * Get the name of the event.
   *
   * @return string
   *   The event name.
   */
  public function getName(): string {
    return $this->eventData->name;
  }

  /**
   * Get the date of the event.
   *
   * @return int
   *   The event date timestamp.
   */
  public function getDate(): int {
    return (int) substr($this->eventData->time, 0, 10);
  }

}
