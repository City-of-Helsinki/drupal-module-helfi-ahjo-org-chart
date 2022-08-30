<?php

namespace Drupal\helfi_ahjo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Routing\ResettableStackedRouteMatchInterface;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "sote_section",
 *   admin_label = @Translation("Sote Section"),
 *   category = @Translation("HELfi"),
 * )
 */
class SoteSection extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The the current primary database.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * ClientFactory constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extension_list_module
   *   The module extension list.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Database\Connection $database
   *   The the current primary database.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager service.
   * @param \Drupal\Core\Routing\ResettableStackedRouteMatchInterface $current_route_match
   *   The current route match service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              array $plugin_definition,
                              ModuleExtensionList $extension_list_module,
                              EntityTypeManagerInterface $entity_type_manager,
                              Connection $database,
                              EntityTypeBundleInfoInterface $entity_type_bundle_info,
                              EntityFieldManagerInterface $entity_field_manager,
                              ResettableStackedRouteMatchInterface $current_route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleExtensionList = $extension_list_module;
    $this->entityTypeManager = $entity_type_manager;
    $this->database = $database;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityFieldManager = $entity_field_manager;
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('extension.list.module'),
      $container->get('entity_type.manager'),
      $container->get('database'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $jsonFile = file_get_contents(
      $this->moduleExtensionList->getPath('helfi_ahjo')
        . '/helsinkiorgchartesimerkki.json');

    $test = $this->levelBelow($jsonFile);
    $term_by_external_id2 = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
      'vid' => 'sote_section',
      'field_external_id' => 'U320200',
    ]);

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'sote_section']);
    foreach ($terms as $item) {
      if (!isset($item->field_external_parent_id->value)
        || $item->field_external_parent_id->value == NULL
        || $item->field_external_parent_id->value == '0') {
        continue;
      }
      $term_by_external_id = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'vid' => 'sote_section',
        'field_external_id' => $item->field_external_parent_id->value,
      ]);

      $item->set('parent', reset($term_by_external_id)->tid->value);
      $item->save();

    }


    $route_tid = $this->getCurrentRoute();
    $max_age = 0;
    $tree = \Drupal::service('helfi_ahjo.taxonomy_utils')->load('sote_section', true);
    return [
      '#theme' => 'hierarchical_taxonomy_menu',
      '#menu_tree' => $tree,
      '#route_tid' => $route_tid,
      '#cache' => [
        'max-age' => $max_age,
        'tags' => [
          'taxonomy_term_list',
        ],
      ],
      '#current_depth' => 0,
      '#vocabulary' => 'sote_section',
      '#max_depth' => 500,
      '#collapsible' => 1,
      '#attached' => [
        'library' => [
          'helfi_ahjo/hierarchical_taxonomy_tree',
        ],
        'drupalSettings' => [
          'interactiveParentMenu' => true,
        ],
      ],
    ];

  }

  /**
   * Gets current route.
   */
  private function getCurrentRoute() {
    if ($term_id = $this->currentRouteMatch->getRawParameter('taxonomy_term')) {
      return $term_id;
    }

    return NULL;
  }


  private function getReferencingFields() {
    $referencing_fields = [];
    $referencing_fields['_none'] = $this->t('- None -');

    $bundles = $this->entityTypeBundleInfo
      ->getBundleInfo('taxonomy_term');

    foreach ($bundles as $bundle => $data) {
      $fields = $this->entityFieldManager
        ->getFieldDefinitions('taxonomy_term', $bundle);

      /** @var \Drupal\Core\Field\FieldDefinitionInterface $field */
      foreach ($fields as $field) {
        if ($field->getType() == 'entity_reference' && $field->getSetting('target_type') == 'taxonomy_term') {
          $referencing_fields[$field->getName()] = $field->getLabel();
        }
      }
    }

    return $referencing_fields;
  }


  function levelBelow($data, &$hierarchy = [], $parentId = NULL) {
    if (!is_array($data)) {
      $data = Json::decode($data);
    }

    foreach ($data as $key => $content) {

      $hierarchy[] = [
        'id' => $content['ID'],
        'parent' => $parentId ?? 0,
        'title' => $content['Name'],
      ];

      $term_by_external_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'vid' => 'sote_section',
        'field_external_id' => $content['ID'],
      ]);

      if (count($term_by_external_name) == 0) {
        $term = Term::create([
          'name' => $content['Name'],
          'vid' => 'sote_section',
          'field_external_id' => $content['ID'],
          'field_external_parent_id' => $parentId ?? 0,
        ]);

        $term->save();
      }
      if (isset($content['OrganizationLevelBelow'])) {
        $this->levelBelow($content['OrganizationLevelBelow'], $hierarchy, $content['ID']);
      }
    }

    return $hierarchy;
  }

}

