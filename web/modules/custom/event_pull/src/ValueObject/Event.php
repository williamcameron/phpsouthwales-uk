<?php

namespace Drupal\event_pull\ValueObject;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * A value object for working with events returned from external sources.
 */
class Event {

  /**
   * The original event data.
   *
   * @var object
   */
  private object $eventData;

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
   * @return string
   *   The formatted event date and time.
   *
   * @throws \Exception
   */
  public function getEventDate(): string {
    return (new \DateTime())
      ->setTimestamp((int) substr($this->eventData->time, 0, 10))
      ->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
  }

  /**
   * Get the venue information.
   *
   * @return \Drupal\event_pull\ValueObject\Venue
   *   The venue model.
   */
  public function getVenue(): Venue {
    return new Venue($this->eventData->venue);
  }

  /**
   * Get the remote ID for the event.
   *
   * @return int
   *   The remote ID.
   */
  public function getRemoteId(): int {
    return $this->eventData->id;
  }

  /**
   * Get the created date for the event.
   *
   * @return int
   *   The created timestamp.
   */
  public function getCreatedDate(): int {
    return (int) substr($this->eventData->created, 0, 10);
  }

  /**
   * Get the event's remote URL.
   *
   * @return string
   *   The remote URL for the event (e.g. the URL on meetup.com).
   */
  public function getRemoteUrl(): string {
    return $this->eventData->link;
  }

  /**
   * Get the event description.
   *
   * @return string
   *   The event description.
   */
  public function getDescription(): string {
    return $this->eventData->description;
  }

  /**
   * Get the number of 'yes' RSVPs.
   *
   * @return int
   *   The RSVP count.
   */
  public function getRsvpCount(): int {
    return $this->eventData->yes_rsvp_count;
  }

  /**
   * Return the Event as an array.
   *
   * @return array
   *   The Event array.
   */
  public function toArray(): array {
    return (array) $this->eventData;
  }

}
