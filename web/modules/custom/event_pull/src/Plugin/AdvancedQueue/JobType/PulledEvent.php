<?php

namespace Drupal\event_pull\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\event_pull\Model\Event;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class PulledEvent extends JobTypeBase implements ContainerFactoryPluginInterface {

  /**
   * The node entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $nodeStorage;

  /**
   * The taxonomy term entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $termStorage;

  /**
   * PulledEvent constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $pluginId
   *   The plugin_id for the plugin instance.
   * @param mixed $pluginDefinition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    array $configuration,
    string $pluginId,
    $pluginDefinition,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->termStorage = $entityTypeManager->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $pluginId,
    $pluginDefinition
  ) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process(Job $job) {

    try {
      $eventData = $job->getPayload();
      $event = new Event((object) $eventData);

      $venue = $this->findOrCreateVenue($event);
      $this->findOrCreateEvent($venue, $event);

      return JobResult::success();
    }
    catch (EntityStorageException $e) {
      return JobResult::failure($e->getMessage());
    }
  }

  /**
   * Create an event term for the event.
   *
   * @param \Drupal\event_pull\Model\Event $event
   *   The event model.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The venue term.
   */
  private function findOrCreateVenue(Event $event): TermInterface {
    $remoteId = $event->getVenue()->getRemoteId();
    $properties = ['field_venue_id' => $remoteId, 'vid' => 'venues'];

    if ($terms = $this->termStorage->loadByProperties($properties)) {
      return collect($terms)->first();
    }

    $values = [
      'field_venue_id' => $event->getVenue()->getRemoteId(),
      'name' => $event->getVenue()->getName(),
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
   * @param \Drupal\event_pull\Model\Event $event
   *   The event model.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The created node.
   */
  private function findOrCreateEvent(TermInterface $venue, Event $event): EntityInterface {
    $remoteId = $event->getRemoteId();
    $properties = ['field_event_id' => $remoteId, 'type' => 'event'];

    if ($terms = $this->nodeStorage->loadByProperties($properties)) {
      return collect($terms)->first();
    }

    $values = [
      'field_event_id' => $remoteId,
      'field_venue' => $venue->id(),
      'status' => NodeInterface::PUBLISHED,
      'type' => 'event',
      'title' => $event->getName(),
    ];

    return tap(Node::create($values), function (NodeInterface $event): void {
      $event->save();
    });
  }

}
