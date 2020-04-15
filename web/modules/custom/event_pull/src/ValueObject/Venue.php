<?php

namespace Drupal\event_pull\ValueObject;

/**
 * A value object for venue data.
 */
class Venue {

  /**
   * The original venue data.
   *
   * @var \Illuminate\Support\Collection
   */
  private $data;

  /**
   * Venue constructor.
   *
   * @param array $venueData
   *   The venue data.
   */
  public function __construct(array $venueData) {
    $this->data = collect($venueData);
  }

  /**
   * Get the venue name.
   *
   * @return string
   *   The venue name.
   */
  public function getName(): string {
    return $this->data->get('name');
  }

  /**
   * Get the remote ID for the venue.
   *
   * @return int
   *   The remote ID.
   */
  public function getRemoteId(): int {
    return $this->data->get('id');
  }

}
