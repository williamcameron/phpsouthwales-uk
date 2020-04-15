<?php

namespace Drupal\event_pull_test\Service\Loader;

use Drupal\event_pull\ValueObject\Event;
use Drupal\event_pull\Service\EventLoader\EventLoaderInterface;
use Illuminate\Support\Collection;

class FakeEventLoader implements EventLoaderInterface {

  /**
   * {@inheritdoc}
   */
  public function getUpcoming(): Collection {
    $data = new \stdClass();
    $data->name = 'Practical Static Analysis';
    $data->link = 'https://www.meetup.com/PHP-South-Wales/events/260287298/';
    $data->description = "<p>This month we have the pleasure of PHP South West organiser David Liddament coming this side of the bridge to give us an exciting talk all about Practical Static Analysis.</p>";
    $data->id = '260287298';
    $data->created = '1554234178000';
    $data->time = '1556643600000';
    $data->venue = [
      'name' => 'Stadium Plaza',
      'id' => '26204581',
    ];
    $data->yes_rsvp_count = 123;

    return collect([
      new Event($data),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl(): string {
    return '';
  }

}
