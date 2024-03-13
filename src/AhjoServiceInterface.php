<?php

namespace Drupal\helfi_ahjo;

use Drupal\Core\Config\ImmutableConfig;

/**
 * Ahjo Service Interface.
 */
interface AhjoServiceInterface {

  /**
   * Return the Ahjo API configs.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   An immutable configuration object.
   */
  public static function getConfig(): ImmutableConfig;

  /**
   * Get data from api and add it as taxonomy terms tree.
   *
   * @param int $orgId
   *   Organisation id if its needed.
   * @param int $maxDepth
   *   Max depth.
   */
  public function fetchDataFromRemote(int $orgId, int $maxDepth);

  /**
   * Create batch operations for taxonomy sote_section.
   *
   * @param array $data
   *   Data for batch.
   */
  public function createTaxonomyBatch(array $data): void;

  /**
   * Recursive set all information from ahjo api.
   *
   * @param array $childData
   *   Child data param.
   * @param array $operations
   *   Operantions param.
   * @param int $externalParentId
   *   External parent id param.
   */
  public function setAllBatchOperations(array $childData, array &$operations, int $externalParentId): void;

  /**
   * Add to cron queue.
   *
   * @param array $data
   *   Data fetched from api.
   * @param object $queue
   *   Queue object.
   * @param int|null $parentId
   *   Parent id if it exists.
   */
  public function addToQueue(array $data, object $queue, int $parentId = NULL);

  /**
   * Create taxonomy tree.
   *
   * @param array|null $excludedByTypeId
   *   Exclude by type id.
   *
   * @return array
   *   Return taxonomy tree.
   */
  public function getDataAsTree($excludedByTypeId);

  /**
   * Create taxonomy terms operation.
   *
   * @param array $data
   *   Data param.
   * @param array $context
   *   Context param.
   */
  public static function syncTaxonomyTermsOperation(array $data, array &$context);

  /**
   * Delete term function.
   *
   * @param array $item
   *   Term item.
   * @param array $context
   *   Context param.
   */
  public static function deleteTaxonomyTermsOperation(array $item, array &$context);

  /**
   * Call batch finished function for batch operation.
   *
   * @param string $success
   *   Success message param.
   * @param array $results
   *   Result param.
   * @param array $operations
   *   Operations param.
   */
  public static function syncTermsBatchFinished(string $success, array $results, array $operations);

  /**
   * Batch operation finished function.
   *
   * @param string $success
   *   Success message param.
   * @param array $results
   *   Results param.
   * @param array $operations
   *   Operations param.
   */
  public static function doSyncTermsBatchFinished(string $success, array $results, array $operations);

}
