<?php

namespace Drupal\event_pull\Plugin\AdvancedQueue\JobType;

use Drupal\advancedqueue\Job;
use Drupal\advancedqueue\JobResult;
use Drupal\advancedqueue\Plugin\AdvancedQueue\JobType\JobTypeBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
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
      $payload = $job->getPayload();

      $venue = $this->findOrCreateVenue($payload);
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
  private function findOrCreateVenue(array $eventData): TermInterface {
    $venueName = $eventData['venue']['name'];
    $properties = ['name' => $venueName, 'vid' => 'venues'];

    if ($terms = $this->termStorage->loadByProperties($properties)) {
      return collect($terms)->first();
    }

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
