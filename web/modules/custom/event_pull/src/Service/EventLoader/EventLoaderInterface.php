<?php

namespace Drupal\event_pull\Service\EventLoader;

use Tightenco\Collect\Support\Collection;

interface EventLoaderInterface {

  /**
   * Get the upcoming events.
   *
   * @return \Tightenco\Collect\Support\Collection
   *   A Collection of Event objects.
   */
  public function getUpcoming(): Collection;

}
