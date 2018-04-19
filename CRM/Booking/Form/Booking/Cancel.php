<?php
use CRM_Booking_ExtensionUtil as E; 
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
    //Set up prevalue
    $bookingAmount = CRM_Booking_BAO_Booking::getBookingAmount($this->_id);
    $this->_values['resource_fee'] = CRM_Utils_Array::value('resource_fees', $bookingAmount);
    $this->_values['sub_resource_fee'] = CRM_Utils_Array::value('sub_resource_fees', $bookingAmount);
    $this->_values['adhoc_charges'] = CRM_Utils_Array::value('adhoc_charges_fees', $bookingAmount);
    $this->_values['discount_amount'] = CRM_Utils_Array::value('discount_amount', $bookingAmount);
    $this->_values['booking_total'] = CRM_Utils_Array::value('total_amount', $bookingAmount);
    $this->assign('booking',$this->_values);
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

    $this->addDate('cancellation_date', E::ts('Date of Cancellation'), TRUE, array('formatType' => 'activityDate'));

    $this->add('hidden', 'booking_total', E::ts('Booking Amount'), array('disabled' => 'disabled'));

    $result = civicrm_api('OptionValue', 'get',  array(
      'version' => 3,
      'option_group_name' => 'booking_cancellation_charges',
    ));
    $cancellationCharges = array();
    foreach ($result['values'] as $key => $ov) {
      $cancellationCharges[$ov['value']] = $ov['label'];
    }
    $this->add('select', 'cancellations', E::ts('Cancellation %'),
      array('' => E::ts('- select -')) + $cancellationCharges,
      TRUE,
      array()
    );

    $this->add('hidden', 'cancellation_charge', E::ts('Cancellation Charge'), array('disabled' => 'disabled'));
    $this->add('text', 'adjustment', E::ts('Additional Charges'));
    $this->add('textarea', 'comment', E::ts('Charge Comment'));

    $this->add('text', 'charge_amount', E::ts('Amount to Pay'), array('disabled' => 'disabled'));

    $this->addFormRule( array( 'CRM_Booking_Form_Booking_Cancel', 'formRule' ), $this );
  }


  static function formRule($params, $files, $self) {
    $errors = parent::rules($params, $files, $self);
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
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
    
    CRM_Booking_BAO_Cancellation::create($params);
    parent::postProcess();
    CRM_Core_Session::setStatus(E::ts('The booking \'%1\' has been cancelled.', array(1 => $this->_values['title'])), E::ts('Saved'), 'success');
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

