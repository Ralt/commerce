<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Tests\CommerceTaxTestBase.
 */

namespace Drupal\commerce_tax\Tests;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\simpletest\WebTestBase;

/**
 * Base trait for commerce_tax tests.
 */
abstract class CommerceTaxTestBase extends WebTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   */
  public static $modules = array('commerce_tax');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalLogin($this->root_user);
  }

}
