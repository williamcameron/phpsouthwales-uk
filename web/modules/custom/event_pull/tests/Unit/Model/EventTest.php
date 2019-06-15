<?php

// @codingStandardsIgnoreFile

namespace Drupal\Tests\event_pull\Model;

use Drupal\event_pull\Model\Event;
use Drupal\Tests\UnitTestCase;

class EventTest extends UnitTestCase {

  /**
   * A test event.
   *
   * @var \Drupal\event_pull\Model\Event
   */
  private $event;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $data = new \stdClass();
    $data->name = 'How to do more with PHPCS';
    $data->time = 1559237400000;

    $this->event = new Event($data);
  }

  /** @test */
  public function get_the_event_name() {
    $this->assertSame('How to do more with PHPCS', $this->event->getName());
  }

  /** @test */
  public function get_the_event_date() {
    $this->assertSame(1559237400, $this->event->getDate());
  }

}
