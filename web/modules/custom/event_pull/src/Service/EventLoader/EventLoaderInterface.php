<?php

namespace Drupal\event_pull\Service\EventLoader;

use Illuminate\Support\Collection;

/**
 * Provides an interface for services to load events.
 */
interface EventLoaderInterface {

  /**
   * Get the upcoming events.
   *
   * @return \Illuminate\Support\Collection
   *   A Collection of Event objects.
   */
  public function getUpcoming(): Collection;

}
