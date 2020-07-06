<?php

namespace Drupal\event_pull\Service\EventLoader;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Load events from Meetup.com.
 */
class MeetupEventLoader extends GuzzleEventLoader {

  /**
   * The event_pull.config configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * Constructs a new MeetupEventLoader.
   */
  public function __construct(ClientInterface $client, ConfigFactoryInterface $configFactory) {
    parent::__construct($client);

    $this->config = $configFactory->get('event_pull.config');
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl(): string {
    return vsprintf('https://api.meetup.com/%s/events?status=%s', [
      $this->config->get('meetup_group_url_name'),
      $this->config->get('meetup_event_statuses'),
    ]);
  }

}
