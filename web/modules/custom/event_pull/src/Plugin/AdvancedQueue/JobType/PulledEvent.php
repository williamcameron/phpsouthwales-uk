<?php

namespace Drupal\event_pull\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;

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
    return JobResult::success();
  }

}
