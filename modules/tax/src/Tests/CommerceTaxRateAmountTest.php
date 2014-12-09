<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Tests\CommerceTaxRateAmountTest.
 */

namespace Drupal\commerce_tax\Tests;

/**
 * Tests the commerce_tax_rate_amount entity forms.
 *
 * @group commerce
 */
class CommerceTaxRateAmountTest extends CommerceTaxTestBase {

  /**
   * Tests the tax rate amount forms.
   */
  public function testTaxRateAmountForms() {
    $tax_type_name = 'test_type';
    $this->createTaxType($tax_type_name);
    $tax_rate_name = 'test_rate';
    $this->createTaxRate($tax_type_name, $tax_rate_name);
    $name = 'test_rate_amount';
    $this->checkTaxRateAmountAddForm($tax_rate_name, $name);
    $this->checkTaxRateAmountEditForm($tax_rate_name, $name);
    $this->checkTaxRateAmountDeleteForm($name);
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
   * Creates a tax rate for the purposes of the test.
   */
  protected function createTaxRate($tax_type_name, $name) {
    $tax_rate = entity_create('commerce_tax_rate', array(
      'id' => $name,
      'type' => $tax_type_name,
      'name' => 'Test rate',
    ));
    return $tax_rate->save();
  }

  /**
   * Checks the tax rate amount add form.
   */
  protected function checkTaxRateAmountAddForm($tax_rate_name, $name) {
    $edit = array(
      'id' => $name,
      'rate' => $tax_rate_name,
      'amount' => '10',
    );

    $this->assertFalse((bool) entity_load('commerce_tax_rate_amount', $name));
    $this->drupalPostForm('admin/commerce/config/tax/amount/' . $tax_type_name . '/add', $edit, $this->t('Save'));
    $this->assertTrue((bool) entity_load('commerce_tax_rate_amount', $name));
  }

  /**
   * Checks the tax rate amount edit form.
   */
  protected function checkTaxRateAmountEditForm($tax_rate_name, $name) {
    $edit = array(
      'id' => $name,
      'rate' => $tax_rate_name,
      'amount' => '20',
    );

    $this->assertFalse(entity_load('commerce_tax_rate_amount', $name)->getAmount() === 20);
    $this->drupalPostForm('admin/commerce/config/tax/amount/' . $name . '/edit', $edit, $this->t('Save'));
    $this->assertTrue(entity_load('commerce_tax_rate_amount', $name)->getAmount() === 20);
  }

  /**
   * Checks the tax rate amount delete form.
   */
  protected function checkTaxRateAmountDeleteForm($name) {
    $edit = array(
      'confirm' => '1',
    );

    $this->assertTrue((bool) entity_load('commerce_tax_rate_amount', $name));
    $this->drupalPostForm('admin/commerce/config/tax/amount/' . $name . '/delete', $edit, $this->('Delete'));
    $this->assertFalse((bool) entity_load('commerce_tax_rate_amount', $name));
  }

}
