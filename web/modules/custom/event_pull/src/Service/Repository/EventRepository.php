<?php

namespace Drupal\event_pull\Service\Repository;

use Drupal\Core\Entity\EntityTypeManager;
use Tightenco\Collect\Support\Collection;

/**
 * A repository for loading event nodes.
 */
class EventRepository {

  private $properties = [
    'type' => 'event',
  ];

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface|object
   */
  private $nodeStorage;

  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->nodeStorage = $entityTypeManager->getStorage('node');
  }

  /**
   * Return all events.
   *
   * @return \Tightenco\Collect\Support\Collection
   *   A collection of events.
   */
  public function getAll(): Collection {
    return collect($this->nodeStorage->loadByProperties($this->properties));
  }

  public function findByRemoteId(int $remoteId): Collection {
    $this->properties['field_event_id'] = $remoteId;

    return collect($this->nodeStorage->loadByProperties($this->properties));
  }
}
