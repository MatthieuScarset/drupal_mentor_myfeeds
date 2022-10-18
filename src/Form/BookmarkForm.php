<?php

namespace Drupal\myfeeds\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form that display a checkbox to bookmark a feed for current user.
 *
 * @internal
 */
class BookmarkForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bookmark';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    $account = \Drupal::currentUser();
    /** @var \Drupal\user\Entity\User $user */
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($account->id());
    $bookmarks = $user->hasField('field_bookmarks') ? explode(', ', $user->get('field_bookmarks')->getString()) : [];
    $bookmarked = in_array($nid, $bookmarks);

    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Bookmark'),
      '#default_value' => $bookmarked,
      '#description' => $bookmarked ? $this->t('Feed already in your bookmarks') : $this->t('Feed not in your bookmarks yet'),
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    // Save node:feed ID for later use in submit.
    $form_state->set('nid', $nid);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Nothing to validate here.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = $this->getCurrentUser();
    if (!$user->hasField('field_bookmarks')) {
      $this->messenger()->addError($this->t('Missing field_bookmarks on user'));
      return;
    }

    $nid = $form_state->get('nid');
    $bookmark = $form_state->getValue('checkbox');
    $bookmarks = $user->get('field_bookmarks')->getValue();
    $new_values = $bookmarks;

    if ($bookmark == '0') {
      // Remove bookmark.
      foreach ($bookmarks as $delta => $value) {
        if ($value['target_id'] == $nid) {
          unset($new_values[$delta]);
        }
      }
    } else {
      // Add bookmark.
      $new_values[] = ['target_id' => $nid];
    }

    // Save new values.
    $user->set('field_bookmarks', $new_values);
    $user->save();

    $this->messenger()->addStatus($this->t('Feed @op from bookmarks', [
      '@op' => ($bookmark == '0') ? $this->t('removed') : $this->t('added'),
    ]));
  }


  /**
   * Helper method to get current user.
   * 
   * @return \Drupal\user\Entity\User
   */
  protected function getCurrentUser() {
    $account = \Drupal::currentUser();
    return \Drupal::entityTypeManager()->getStorage('user')->load($account->id());
  }
}
