<?php

namespace Drupal\event_pull\Model;

/**
 * A value object for venue data.
 */
class Venue {

  /**
   * The original venue data.
   *
   * @var \Tightenco\Collect\Support\Collection
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

}
