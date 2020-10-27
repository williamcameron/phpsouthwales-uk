<?php

namespace Drupal\event_pull\Service\Repository;

use Drupal\Core\Entity\EntityTypeManager;
use Illuminate\Support\Collection;

/**
 * A repository for loading event nodes.
 */
class EventRepository {

  /**
   * The properties to load events for.
   *
   * @var array
   */
  private array $properties = [
    'type' => 'event',
  ];

  /**
   * The node entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $nodeStorage;

  /**
   * EventRepository constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   The entity type manager service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->nodeStorage = $entityTypeManager->getStorage('node');
  }

  /**
   * Return all events.
   *
   * @return \Illuminate\Support\Collection
   *   A collection of events.
   */
  public function getAll(): Collection {
    return collect($this->nodeStorage->loadByProperties($this->properties));
  }

  /**
   * Find an event by it’s remote ID.
   */
  public function findByRemoteId(int $remoteId): Collection {
    $this->properties['field_event_id'] = $remoteId;

    return collect($this->nodeStorage->loadByProperties($this->properties));
  }

}
