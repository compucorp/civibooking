<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_Search extends CRM_Core_Form {

  function buildQuickForm() {
    $this->add('text', 'title', ts('Booking title'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  function postProcess() {}

}
