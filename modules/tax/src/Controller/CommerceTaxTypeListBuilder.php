<?php

/**
 * @file
 * Contains \Drupal\commerce_tax\Controller\CommerceTaxTypeListBuilder.
 */

namespace Drupal\commerce_tax\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of tax types.
 */
class CommerceTaxTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Machine name');
    $header['name'] = $this->t('Name');
    $header['tag'] = $this->t('Tag');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->getId();
    $row['name'] = $this->getLabel($entity);
    $row['tag'] = $entity->getTag();
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
    $operations = parent::getDefaultOperations($entity);

    $rates_route = Url::fromRoute('entity.commerce_tax_rate.list', array(
      'commerce_tax_type' => $entity->getId()
    ));
    $add_rate_route = Url::fromRoute('entity.commerce_tax_rate.add_form', array(
      'commerce_tax_type' => $entity->getId(),
    ));

    $operations['rates'] = array(
      'title' => $this->t('View rates'),
      'url' => $rates_route,
    );
    $operations['add_rate'] = array(
      'title' => $this->t('Add rate'),
      'url' => $add_rate_route,
    );

    return $operations;
  }

}
