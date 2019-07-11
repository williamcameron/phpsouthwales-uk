<?php

namespace Drupal\Tests\event_pull\Model;

use Drupal\event_pull\Model\Event;
use Drupal\Tests\UnitTestCase;

/**
 * Event model tests.
 *
 * @coversDefaultClass \Drupal\event_pull\Model\Event
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
    $data->link = 'https://www.meetup.com/PHP-South-Wales/events/261255847';
    $data->description = '<p>Some text about the event.</p>';

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

  /**
   * Test getting the remote URL.
   *
   * @group event-pull
   * @group event-pull-model
   * @group event-pull-model-event
   *
   * @covers ::getRemoteUrl
   */
  public function testGetRemoteUrl() {
    $this->assertSame('https://www.meetup.com/PHP-South-Wales/events/261255847', $this->event->getRemoteUrl());
  }

  /**
   * Test getting the event description.
   *
   * @group event-pull
   * @group event-pull-model
   * @group event-pull-model-event
   *
   * @covers ::getDescription
   */
  public function testGetDescription() {
    $this->assertSame('<p>Some text about the event.</p>', $this->event->getDescription());
  }

}
