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

  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->add('text', 'event_date', ts('Date of event'), array('disabled' => 'disabled'));
    $this->addDate('cancellation_date', ts('Date of cancellation'), FALSE, array('formatType' => 'activityDate'));

    $this->add('text', 'resource_fee', ts('Resource fees'), array('disabled' => 'disabled'));
    $this->add('text', 'sub_resource_fee', ts('Sub resoruce fees'), array('disabled' => 'disabled'));
    $this->add('text', 'adhoc_charges', ts('Ad-hoc charges'), array('disabled' => 'disabled'));
    $this->add('text', 'discount_amount', ts('Discount amount'), array('disabled' => 'disabled'));
    $this->add('text', 'booking_total', ts('Booking value'), array('disabled' => 'disabled'));

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
      FALSE,
      array()
    );

    $this->add('text', 'cancellation_charge', ts('Cancellation charge'), array('disabled' => 'disabled'));
    $this->add('text', 'adjustment', ts('Additional Charges/Adjectments'));
    $this->add('textarea', 'comment', ts('Charge comment'));

    $this->add('text', 'charge_amount', ts('Amount to pay'), array('disabled' => 'disabled'));

  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    list($resourceFees, $subResourceFees, $adhocCharges, $totalAmount) = CRM_Booking_BAO_Booking::getBookingAmount($this->_id);
    return $defaults;
  }

}

