<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 * This class generates form components for Booking
 *
 */
class CRM_Booking_Form_Booking_Cancel extends CRM_Booking_Form_Booking_Base {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    parent::preProcess();
    self::registerScripts();
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->add('text', 'event_date', ts('Date of Event'), array('disabled' => 'disabled'));
    $this->addDate('cancellation_date', ts('Date of Cancellation'), TRUE, array('formatType' => 'activityDate'));

    $this->add('text', 'resource_fee', ts('Resource Fees'), array('disabled' => 'disabled'));
    $this->add('text', 'sub_resource_fee', ts('Unlimited Resource Fees'), array('disabled' => 'disabled'));
    $this->add('text', 'adhoc_charges', ts('Ad-hoc Charges'), array('disabled' => 'disabled'));
    $this->add('text', 'discount_amount', ts('Discount Amount'), array('disabled' => 'disabled'));
    $this->add('text', 'booking_total', ts('Booking Amount'), array('disabled' => 'disabled'));

    $result = civicrm_api('OptionValue', 'get',  array(
      'version' => 3,
      'option_group_name' => 'booking_cancellation_charges',
    ));
    $cancellationCharges = array();
    foreach ($result['values'] as $key => $ov) {
      $cancellationCharges[$ov['value']] = $ov['label'];
    }
    $this->add('select', 'cancellations', ts('Cancellation %'),
      array('' => ts('- select -')) + $cancellationCharges,
      TRUE,
      array()
    );

    $this->add('text', 'cancellation_charge', ts('Cancellation Charge'), array('disabled' => 'disabled'));
    $this->add('text', 'adjustment', ts('Additional Charges'));
    $this->add('textarea', 'comment', ts('Charge Comment'));

    $this->add('text', 'charge_amount', ts('Amount to Pay'), array('disabled' => 'disabled'));

    $this->addFormRule( array( 'CRM_Booking_Form_Booking_Cancel', 'formRule' ), $this );
  }


  static function formRule($params, $files, $self) {
    $errors = parent::rules($params, $files, $self);
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    $bookingAmount = CRM_Booking_BAO_Booking::getBookingAmount($this->_id);
    $defaults['resource_fee'] = CRM_Utils_Array::value('resource_fees', $bookingAmount);
    $defaults['sub_resource_fee'] = CRM_Utils_Array::value('sub_resource_fees', $bookingAmount);
    $defaults['adhoc_charges'] = CRM_Utils_Array::value('adhoc_charges_fees', $bookingAmount);
    $defaults['booking_total'] = CRM_Utils_Array::value('total_amount', $bookingAmount);
    return $defaults;
  }

  function postProcess(){
    CRM_Utils_System::flushCache();
    $values = $this->exportValues();
    $params['booking_id'] = $this->_id;
    $params['cancellation_date'] = $values['cancellation_date'];
    $params['cancellation_percentage'] = $values['cancellations'];
    $params['booking_total'] = $values['booking_total'];
    $params['cancellation_fee'] = ($params['cancellation_percentage'] / 100) * $params['booking_total'];
    $params['additional_charge'] = $values['adjustment'];
    $params['comment'] = $values['comment'];
    civicrm_api3('Cancellation', 'create', $params);
    parent::postProcess();
    CRM_Core_Session::setStatus(ts('The booking \'%1\' has been cancelled.', array(1 => $this->_values['title'])), ts('Saved'), 'success');
  }

  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'templates/CRM/Booking/Form/Booking/Cancel.js', 170, 'html-header');
  }
}

