<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Form\CommerceTaxRateAmountForm.
 */

namespace Drupal\commerce_tax\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_tax\Entity\CommerceTaxRate;

class CommerceTaxRateAmountForm extends EntityForm {

  /**
   * The tax rate amount storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $taxRateAmountStorage;

  /**
   * The tax rate storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $taxRateStorage;

  /**
   * Creates a CommerceTaxRateAmountForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $tax_rate_amount_storage
   *   The tax rate amount storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $tax_rate_storage
   *   The tax rate storage.
   */
  public function __construct(EntityStorageInterface $tax_rate_amount_storage, EntityStorageInterface $tax_rate_storage) {
    $this->taxRateAmountStorage = $tax_rate_amount_storage;
    $this->taxRateStorage = $tax_rate_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($entity_manager->getStorage('commerce_tax_rate_amount'), $entity_manager->getStorage('commerce_tax_rate'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $tax_rate_amount = $this->entity;

    $form['rate'] = array(
      '#type' => 'hidden',
      '#value' => $tax_rate_amount->getRate(),
    );
    $form['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Machine name'),
      '#default_value' => $tax_rate_amount->getId(),
      '#element_validate' => array('::validateId'),
      '#description' => $this->t('Only lowercase, underscore-separated letters allowed.'),
      '#pattern' => '[a-z_]+',
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['amount'] = array(
      '#type' => 'number',
      '#title' => $this->t('Amount'),
      '#default_value' => $tax_rate_amount->getAmount(),
      '#element_validate' => array('::validateAmount'),
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['startDate'] = array(
      '#type' => 'date',
      '#title' => $this->t('Start date'),
      '#default_value' => $tax_rate_amount->getStartDate(),
    );
    $form['endDate'] = array(
      '#type' => 'date',
      '#title' => $this->t('End date'),
      '#default_value' => $tax_rate_amount->getEndDate(),
    );

    return $form;
  }

  /**
   * Validates the id field.
   */
  public function validateId(array $element, FormStateInterface $form_state, array $form) {
    $tax_rate_amount = $this->getEntity();
    $id = $element['#value'];
    if (!preg_match('/[a-z_]+/', $id)) {
      $form_state->setError($element, $this->t('The machine name must be in lowercase, underscore-separated letters only.'));
    }
    elseif ($tax_rate_amount->isNew()) {
      $loaded_tax_rate_amounts = $this->taxRateAmountStorage->loadByProperties(array(
        'id' => $id,
      ));
      if ($loaded_tax_rate_amounts) {
        $form_state->setError($element, $this->t('The machine name is already in use.'));
      }
    }
  }

  /**
   * Validates the amount field.
   */
  public function validateAmount(array $element, FormStateInterface $form_state, array $form) {
    if (!is_numeric($element['#value'])) {
      $form_state->setError($element, $this->t('The amount must be numeric.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $tax_rate_amount = $this->entity;

    try {
      $tax_rate_amount->save();
      drupal_set_message($this->t('Saved the %label tax rate.', array(
        '%label' => $tax_rate_amount->label(),
      )));

      $tax_rate = $this->taxRateStorage->load($tax_rate_amount->getRate());
      try {
        if (!$tax_rate->hasAmount($tax_rate_amount)) {
          $tax_rate->addAmount($tax_rate_amount);
          $tax_rate->save();
        }

        $form_state->setRedirect('entity.commerce_tax_rate_amount.list', array(
          'commerce_tax_rate' => $tax_rate->getId(),
        ));
      }
      catch (\Exception $e) {
        drupal_set_message($this->t('The %label tax rate was not saved.', array(
          '%label' => $tax_rate->label(),
        )));
        $this->logger('commerce_tax')->error($e);
        $form_state->setRebuild();
      }

    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The %label tax rate amount was not saved.', array(
        '%label' => $tax_rate->label()
      )), 'error');
      $this->logger('commerce_tax')->error($e);
      $form_state->setRebuild();
    }
  }

}
