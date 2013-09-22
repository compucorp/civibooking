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

      $this->addElement('checkbox', 'record_contribution', ts('Record Booking Payment?'));

      $paymentContacts =  array('' => ts('- select -'),
                                '1' => ts('Primary contact'),
                                '2' => ts('Secondary contact'));
      $this->add('select', 'select_payment_contact', ts('Select contact'),
        $paymentContacts, FALSE,
        array(
          'id' => 'select_payment_contact',
        )
      );


      $this->addDate('receive_date', ts('Received'), FALSE, array('formatType' => 'activityDate'));


      $this->assign('amount', 300);

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
    }



  }
}

