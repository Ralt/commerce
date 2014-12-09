<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Tests\CommerceTaxRateTest.
 */

namespace Drupal\commerce_tax\Tests;

/**
 * Tests the commerce_tax_rate entity forms.
 *
 * @group commerce
 */
class CommerceTaxRateTest extends CommerceTaxTestBase {

  /**
   * Tests the tax rate forms.
   */
  public function testTaxRateForms() {
    $tax_type_name = 'test_type';
    $this->createTaxType($tax_type_name);
    $name = 'test_rate';
    $this->checkTaxRateAddForm($tax_type_name, $name);
    $this->checkTaxRateEditForm($tax_type_name, $name);
    $this->checkTaxRateDeleteForm($name);
  }

  /**
   * Creates a tax type for the purposes of the test.
   */
  protected function createTaxType($name) {
    $tax_type = entity_create('commerce_tax_type', array(
      'id' => $name,
      'name' => 'Test type',
      'roundingMode' => '1',
      'tag' => 'test',
    ));
    return $tax_type->save();
  }

  /**
   * Checks the tax rate add form.
   */
  protected function checkTaxRateAddForm($tax_type_name, $name) {
    $edit = array(
      'id' => $name,
      'type' => $tax_type_name,
      'name' => 'Test rate',
    );

    $this->assertFalse((bool) entity_load('commerce_tax_rate', $name));
    $this->drupalPostForm('admin/commerce/config/tax/rate/' . $tax_type_name . '/add', $edit, $this->t('Save'));
    $this->assertTrue((bool) entity_load('commerce_tax_rate', $name));
  }

  /**
   * Checks the tax rate edit form.
   */
  protected function checkTaxRateEditForm($tax_type_name, $name) {
    $edit = array(
      'id' => $name,
      'type' => $tax_type_name,
      'name' => 'rate test',
    );

    $this->assertFalse(entity_load('commerce_tax_rate', $name)->getName() === 'rate test');
    $this->drupalPostForm('admin/commerce/config/tax/rate/' . $name . '/edit', $edit, $this->t('Save'));
    $this->assertTrue(entity_load('commerce_tax_rate', $name)->getName() === 'rate test');
  }

  /**
   * Checks the tax rate delete form.
   */
  protected function checkTaxRateDeleteForm($name) {
    $edit = array(
      'confirm' => '1',
    );

    $this->assertTrue((bool) entity_load('commerce_tax_rate', $name));
    $this->drupalPostForm('admin/commerce/config/tax/rate/' . $name . '/delete', $edit, $this->('Delete'));
    $this->assertFalse((bool) entity_load('commerce_tax_rate', $name));
  }

}
