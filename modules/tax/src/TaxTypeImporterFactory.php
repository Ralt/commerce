<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\TaxTypeImporterFactory.
 */

namespace Drupal\commerce_tax;

use \Drupal\Core\Entity\EntityManagerInterface;

class TaxTypeImporterFactory implements TaxTypeImporterFactoryInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs the factory.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($tax_types_folder) {
    return new TaxTypeImporter($this->entityManager, $tax_types_folder);
  }

}
