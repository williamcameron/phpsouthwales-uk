<?php

namespace Drupal\Tests\event_pull\Model;

use Drupal\event_pull\Model\Event;
use Drupal\Tests\UnitTestCase;

/**
 * Event model tests.
 */
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
    $data->created = 1557234224000;
    $data->time = 1559237400000;

    $this->event = new Event($data);
  }

  /**
   * Test retrieving the event name.
   */
  public function testGetEventName() {
    $this->assertSame('How to do more with PHPCS', $this->event->getName());
  }

  /**
   * Test retrieving the event date.
   */
  public function testGetEventDate() {
    $this->assertSame(1559237400, $this->event->getDate());
  }

  /**
   * Test retrieving the event created date.
   */
  public function testGetCreatedDate() {
    $this->assertSame(1557234224, $this->event->getCreatedDate());
  }

}
