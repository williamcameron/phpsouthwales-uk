<?php

namespace Drupal\event_pull\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;

/**
 * A queue job type for pulled events.
 *
 * Creates event nodes for external events that have been pulled in.
 *
 * @AdvancedQueueJobType(
 *   id = "event_pull_pulled_event",
 *   label = @Translation("Pulled event"),
 * )
 */
class PulledEvent extends JobTypeBase {

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {

    try {
      $payload = $job->getPayload();

      $venue = $this->createVenue($payload);
      $this->createNode($venue, $payload);

      return JobResult::success();
    }
    catch (EntityStorageException $e) {
      return JobResult::failure($e->getMessage());
    }
  }

  /**
   * Create an event term for the event.
   *
   * @param array $eventData
   *   The event data.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The venue term.
   */
  private function createVenue(array $eventData): TermInterface {
    $values = [
      'name' => $eventData['venue']['name'],
      'vid' => 'venues',
    ];

    return tap(Term::create($values), function (TermInterface $venue): void {
      $venue->save();
    });
  }

  /**
   * Create the event node.
   *
   * @param \Drupal\taxonomy\TermInterface $venue
   *   The venue term.
   * @param array $eventData
   *   The event data.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The created node.
   */
  private function createNode(TermInterface $venue, array $eventData): EntityInterface {
    $values = [
      'field_venue' => $venue->id(),
      'status' => NodeInterface::PUBLISHED,
      'type' => 'event',
      'title' => $eventData['name'],
    ];

    return tap(Node::create($values), function (NodeInterface $event): void {
      $event->save();
    });
  }

}
