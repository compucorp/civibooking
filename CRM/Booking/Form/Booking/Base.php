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
 * This base class for Update/Cancel/Delete Booking
 *
 */
abstract class CRM_Booking_Form_Booking_Base extends CRM_Core_Form {

  protected $_id;

  protected $_cid;

  protected $_values;


  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->_id = $this->get('id');
    $this->_cid = $this->get('cid');
    $params        = array('id' => $this->_id);

    CRM_Booking_BAO_Booking::retrieve($params, $this->_values );

    if (empty($this->_values)) {
      CRM_Core_Error::statusBounce(ts('The requested booking record does not exist (possibly the record was deleted).'));
    }

  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => ts('Delete'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ),
        )
      );
    }
    elseif ($this->_action & CRM_Core_Action::VIEW){
      $this->addButtons(array(
          array(
            'type' => 'cancel',
            'name' => ts('Done'),
            'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            'isDefault' => TRUE,
          ),
        )
      );
    }
    else {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => ts('Save'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ),
        )
      );
    }


    if (($this->_action & CRM_Core_Action::DELETE) || ($this->_action & CRM_Core_Action::VIEW)) {
      return;
    }else{

      $this->addElement('checkbox', 'record_contribution', ts('Record Payment?'));
      $paymentContacts =  array('' => ts('- select -'),
                                $this->_values['primary_contact_id'] => CRM_Contact_BAO_Contact::displayName($this->_values['primary_contact_id']));
      if($this->_values['secondary_contact_id']){
        $paymentContacts[$this->_values['secondary_contact_id']] =  CRM_Contact_BAO_Contact::displayName($this->_values['secondary_contact_id']);
      }
      $this->add('select', 'select_payment_contact', ts('Select contact'),
        $paymentContacts, FALSE,
        array(
          'id' => 'select_payment_contact',
        )
      );

      $this->addDate('receive_date', ts('Received'), FALSE, array('formatType' => 'activityDate'));

      $this->add('text', 'total_amount', ts('Amount'), array( 'disabled' => 'disabled' ));

      $this->add('select', 'financial_type_id',
        ts('Financial Type'),
        array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::financialType()
      );

      $this->add('select', 'payment_instrument_id',
          ts('Paid By'),
          array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::paymentInstrument(),
          FALSE,
          array()
      );

      $this->add('text', 'trxn_id', ts('Transaction ID'));

      $this->add('select', 'contribution_status_id',
          ts('Payment Status'),
          array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::contributionStatus(),
          FALSE,
          array()
      );

      $this->addElement('checkbox', 'send_email_receipt', ts('Send email receipt?'));

    }
  }

   /**
   * This function sets the default values for the form. that in edit mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    if (($this->_action & CRM_Core_Action::DELETE) || ($this->_action & CRM_Core_Action::VIEW)) {
      return;
    }else{
      $defaults = array();
      CRM_Booking_BAO_Payment::retrieve($params = array('booking_id' => $this->_id), $payment);
      if(!empty($payment) && isset($payment['contribution_id'])){ //payment exist
        $defaults['record_contribution'] = 1;
        $params = array(
          'version' => 3,
          'id' => $payment['contribution_id'],
        );
        $result = civicrm_api('Contribution', 'get', $params);
        $contribution = CRM_Utils_Array::value($payment['contribution_id'], $result['values'] );
        $defaults['select_payment_contact'] = $contribution['contact_id'];
        //$defaults['receive_date'] = $contribution['receive_date']; //fixed received date
        $defaults['total_amount'] = $contribution['total_amount'];
        $defaults['trxn_id'] = $contribution['trxn_id'];
        $defaults['financial_type_id'] = $contribution['financial_type_id'];
        //TODO:: the instrument id return wrong value
        $defaults['payment_instrument_id'] = $contribution['instrument_id'];
        $defaults['contribution_status_id'] = $contribution['contribution_status_id'];
      }
      if ($this->_action & CRM_Core_Action::CLOSE){
        $defaults = $this->_values;
      }
      return $defaults;

    }
  }

}

