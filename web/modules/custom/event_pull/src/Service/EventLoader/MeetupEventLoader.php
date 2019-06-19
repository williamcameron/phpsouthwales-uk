<?php

namespace Drupal\event_pull\Service\EventLoader;

/**
 * Load events from Meetup.com.
 */
class MeetupEventLoader extends GuzzleEventLoader {

  /**
   * {@inheritdoc}
   */
  public function getUrl(): string {
    return sprintf('https://api.meetup.com/%s/events?status=past,upcoming', getenv('MEETUP_GROUP_URL_NAME'));
  }

}
