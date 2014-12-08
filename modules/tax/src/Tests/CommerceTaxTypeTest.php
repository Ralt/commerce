<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Tests\CommerceTaxTypeTest.
 */

namespace Drupal\commerce_tax\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Tests the commerce_tax_type entity forms.
 *
 * @group commerce
 */
class CommerceTaxTypeTest extends WebTestBase {

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

  /**
   * Checks that the default tax types are correctly imported.
   */
  public function testDefaultTaxTypes() {
    $this->checkDefaultAdminPage();
    $this->checkDefaultConfig();
  }

  /**
   * Checks that the default tax types exist on the admin page.
   */
  protected function checkDefaultAdminPage() {
    $this->drupalGet('admin/commerce/config/tax/type');

    $machine_names = array('sales_tax', 'vat');
    foreach ($machine_names as $i => $name) {
      $this->assertText($name, $name . ' exists');
    }

    $names = array('Sales tax', 'VAT');
    foreach ($names as $name) {
      $this->assertText($name, $name . ' exists');
    }

    $tags = array('sales', 'vat');
    foreach ($tags as $tag) {
      $this->assertText($tag, $tag . ' exists');
    }
  }

  /**
   * Checks that the default tax types exist in the config.
   */
  protected function checkDefaultConfig() {
    $this->assertTrue((bool) \Drupal::config('commerce_tax.commerce_tax_type.sales_tax'));
    $this->assertTrue((bool) \Drupal::config('commerce_tax.commerce_tax_type.vat'));
    $this->assertTrue(\Drupal::config('commerce_tax.commerce_tax_type.sales_tax')->get('name') === $this->t('Sales tax'));
  }

}
