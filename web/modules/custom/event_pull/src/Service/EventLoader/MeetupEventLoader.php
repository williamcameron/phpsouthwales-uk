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
    return vsprintf('https://api.meetup.com/%s/events?status=%s', [
      env('MEETUP_GROUP_URL_NAME'),
      env('MEETUP_EVENT_STATUSES', 'upcoming'),
    ]);
  }

}
