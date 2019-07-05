<?php

namespace Drupal\event_pull\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

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

      $event = $this->createNode($payload);

      return JobResult::success();
    }
    catch (EntityStorageException $e) {
      return JobResult::failure($e->getMessage());
    }
  }

  /**
   * Create the event node.
   *
   * @param array $eventData
   *   The event data.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\node\Entity\Node
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function createNode(array $eventData) {
    $node = Node::create([
      'status' => NodeInterface::PUBLISHED,
      'type' => 'event',
      'title' => $eventData['name'],
    ]);

    $node->save();

    return $node;
  }

}
