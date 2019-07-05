<?php

namespace Drupal\event_pull\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
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

      $node = Node::create([
        'status' => NodeInterface::PUBLISHED,
        'type' => 'event',
        'title' => $payload['name'],
      ]);
      $node->save();

      return JobResult::success();
    }
    catch (EntityStorageException $e) {
      return JobResult::failure($e->getMessage());
    }
  }

}
