<?php

/**
 * @file
 * Contains \Drupal\deploy\Entity\Form\ReplicationForm.
 */

namespace Drupal\deploy\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Replication edit forms.
 *
 * @ingroup deploy
 */
class ReplicationForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    if ($this->getDefaultSource()) {
      $form['source']['widget']['#default_value'] = $this->getDefaultSource();
    }
    if ($this->getDefaultTarget()) {
      $form['target']['widget']['#default_value'] = $this->getDefaultTarget();
    }
    $form['actions']['submit']['#value'] = t('Review');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label deployment.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label deployment.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.replication.canonical', ['replication' => $entity->id()]);
  }

  protected function getDefaultSource() {
    /** @var \Drupal\multiversion\Entity\Workspace $workspace; **/
    $workspace = \Drupal::service('workspace.manager')->getActiveWorkspace();
    return 'workspace:' . $workspace->id();
  }

  protected function getDefaultTarget() {
    /** @var \Drupal\multiversion\Entity\Workspace $workspace; **/
    $workspace = \Drupal::service('workspace.manager')->getActiveWorkspace();
    /** @var \Drupal\multiversion\Entity\Workspace $upstream; **/
    $upstream = $workspace->get('upstream')->entity;
    if (!$upstream) {
      return null;
    }
    return 'workspace:' . $upstream->id();
  }
}