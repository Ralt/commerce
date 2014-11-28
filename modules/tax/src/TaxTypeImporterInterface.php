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
   *
   * @param string $tax_types_folder
   *   The folder where the tax types definitions are.
   */
  public function import($tax_types_folder);

}
