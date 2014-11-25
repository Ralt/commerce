<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Form\CommerceTaxRateForm.
 */

namespace Drupal\commerce_tax\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_tax\Entity\CommerceTaxRate;
use CommerceGuys\Tax\Model\TaxTypeInterface;

class CommerceTaxRateForm extends EntityForm {

  /**
   * The tax rate storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $taxRateStorage;

  /**
   * Creates a CommerceTaxRateForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $tax_rate_storage
   *   The tax rate storage.
   */
  public function __construct(EntityStorageInterface $tax_rate_storage) {
    $this->taxRateStorage = $tax_rate_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($entity_manager->getStorage('commerce_tax_rate'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $tax_rate = $this->entity;

    $form['type'] = array(
      '#type' => 'hidden',
      '#value' => $tax_rate->getType(),
    );
    $form['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Machine name'),
      '#default_value' => $tax_rate->getId(),
      '#element_validate' => array('::validateId'),
      '#description' => $this->t('Only lowercase, underscore-separated letters allowed.'),
      '#pattern' => '[a-z_]+',
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $tax_rate->getName(),
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['display_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Display name'),
      '#default_value' => $tax_rate->getDisplayName(),
    );
    $form['default'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Defaultness'),
      '#default_value' => $tax_rate->isDefault(),
      '#element_validate' => array('::validateDefaultness'),
    );

    return $form;
  }

  /**
   * Validates the id field.
   */
  public function validateId(array $element, FormStateInterface &$form_state, array $form) {
    $tax_rate = $this->getEntity();
    $id = $element['#value'];
    if (!preg_match('/[a-z_]+/', $id)) {
      $form_state->setError($element, $this->t('The machine name must be in lowercase, underscore-separated letters only.'));
    }
    elseif ($tax_rate->isNew()) {
      $loaded_tax_rates = $this->taxRateStorage->loadByProperties(array(
        'id' => $id,
      ));
      if ($loaded_tax_rates) {
        $form_state->setError($element, $this->t('The machine name is already in use.'));
      }
    }
  }

  /**
   * Validates that there is only one default.
   */
  public function validateDefaultness(array $element, FormStateInterface &$form_state, array $form) {
    $tax_rate = $this->getEntity();
    $default = $element['#value'];
    if ($default) {
      $loaded_tax_rates = $this->taxRateStorage->loadByProperty(array(
        'type' => $form_state['values']['type']
      ));
      foreach ($loaded_tax_rates as $tax_rate) {
        if ($tax_rate->isDefault()) {
          $form_state->setError($element, $this->t('Tax rate %label is already the default.', array(
            '%label' => $tax_rate->label(),
          )));
          break;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $tax_rate = $this->entity;

    try {
      $tax_rate->save();
      drupal_set_message($this->t('Saved the %label tax rate.', array(
            '%label' => $tax_rate->label(),
          )));
      $form_state->setRedirect('entity.commerce_tax_rate.list');
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The %label tax rate was not saved.', array(
            '%label' => $tax_rate->label()
          )), 'error');
      $this->logger('commerce_tax')->error($e);
      $form_state->setRebuild();
    }
  }

}
