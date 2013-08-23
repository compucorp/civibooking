<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_BookingDetail extends CRM_Core_Form {

  /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return ts('Booking detail');
  }


  function preProcess(){}

  function buildQuickForm() {
    parent::buildQuickForm();

     //CRM_Contact_Form_NewContact::buildQuickForm($form);


    $buttons = array(
      array('type' => 'back',
        'name' => ts('<< Back'),
      ),
      array(
        'type' => 'submit',
        'name' => ts('Complate and save'),
      ),

    );

    $this->addButtons($buttons);

  }

  function postProcess() {
    $values = $this->exportValues();

    dprint_r($this);
    exit;

    parent::postProcess();
  }


}
