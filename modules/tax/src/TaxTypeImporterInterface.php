<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\TaxTypeImporterInterface.
 */

namespace Drupal\commerce_tax;

/**
 * Defines a tax type importer.
 */
interface TaxTypeImporterInterface {

  /**
   * Imports all the tax types defined in a folder.
   */
  public function import();

}
