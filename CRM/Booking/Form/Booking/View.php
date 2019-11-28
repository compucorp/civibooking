<?php
use CRM_Booking_ExtensionUtil as E;

/**
 * This class generates form components for Booking
 *
 */
class CRM_Booking_Form_Booking_View extends CRM_Booking_Form_Booking_Base {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    parent::preProcess();

    $details = CRM_Booking_BAO_Booking::getBookingDetails($this->_id);

    $this->_values['slots'] =  CRM_Utils_Array::value('slots', $details);
    $this->_values['sub_slots'] = CRM_Utils_Array::value('sub_slots', $details);
    $this->_values['adhoc_charges'] = CRM_Utils_Array::value('adhoc_charges', $details);
    $this->_values['cancellation_charges'] = CRM_Utils_Array::value('cancellation_charges', $details);
    $this->_values['contribution'] = CRM_Utils_Array::value('contribution', $details);
    $this->_values['sub_total'] = CRM_Utils_Array::value('total_amount', $this->_values) + CRM_Utils_Array::value('discount_amount', $this->_values); //total_amount has been deducted from discount

    $this->assign($this->_values);

    $displayName = CRM_Contact_BAO_Contact::displayName($this->_values['primary_contact_id']);
    $secondaryContactDisplayName = CRM_Contact_BAO_Contact::displayName( CRM_Utils_Array::value('secondary_contact_id', $this->_values));

    $this->assign('displayName', $displayName);
    $this->assign('secondaryContactDisplayName',$secondaryContactDisplayName );
    $this->assign('contact_id', $this->_cid);

    $params = array(
      'option_group_name' => CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS,
      'name' => CRM_Booking_Utils_Constants::OPTION_VALUE_CANCELLED,
    );
    $result = civicrm_api3('OptionValue', 'get', $params);
    $this->_cancelStatusId =  $cancelStatus = CRM_Utils_Array::value('value', CRM_Utils_Array::value($result['id'], $result['values']));

    if ($this->_values['status_id'] == $cancelStatus){
      $this->assign('is_cancelled', TRUE);
    }
    // omitting contactImage from title for now since the summary overlay css doesn't work outside of our crm-container
    CRM_Utils_System::setTitle(E::ts('View Booking for') .  ' ' . $displayName);

    self::registerScripts($this);

  }

  static function registerScripts($ctx) {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    $snippet = CRM_Utils_Request::retrieve('snippet', 'Positive',
      $ctx, FALSE, 0
    );
    if ($snippet == 2) {
      CRM_Core_Resources::singleton()
        ->addStyleFile(E::LONG_NAME, 'css/booking.print.css', 10, 'page-header');
    }

  }
}

