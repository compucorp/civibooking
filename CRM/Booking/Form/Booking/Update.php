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
class CRM_Booking_Form_Booking_Update extends CRM_Booking_Form_Booking_Base {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    parent::preProcess();
    $this->assign('booking', $this->_values);
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
    if($this->_action & CRM_Core_Action::UPDATE){
      $bookingStatus =  CRM_Booking_BAO_Booking::buildOptions('status_id', 'create');
      
      if($this->_values['status_id'] == $this->_cancelStatusId){
        $this->assign('cancel_status_id', $this->_cancelStatusId);
        $this->add('hidden', 'booking_status', $this->_values['status_id']);
      }else{
        unset($bookingStatus[$this->_cancelStatusId]); //remove cancelled option
        $this->add('select', 'booking_status', E::ts('Booking status'),
          array('' => E::ts('- select -')) + $bookingStatus,
          TRUE,
          array()
        );
      }
    }
    $this->addFormRule( array( 'CRM_Booking_Form_Booking_Update', 'formRule' ), $this );

  }

  static function formRule($params, $files, $self) {
    $errors = parent::rules($params, $files, $self);
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if ($this->_action & CRM_Core_Action::UPDATE || $this->_values['status_id'] == $this->_cancelStatusId) {
      $defaults['booking_status'] = $this->_values['status_id'];
    }
    return $defaults;
  }


  function postProcess(){
    CRM_Utils_System::flushCache();
    if ($this->_action & CRM_Core_Action::DELETE) {
      civicrm_api3('Booking', 'delete', array('id' => $this->_id));
      CRM_Core_Session::setStatus(E::ts('Selected booking has been deleted.'), E::ts('Record Deleted'), 'success');
    }
    else {
      $values = $this->exportValues();
      $params['id'] = $this->_id;
      $params['status_id'] = $values['booking_status'];
      $booking = civicrm_api3('Booking', 'create', $params);
      parent::postProcess();
      CRM_Core_Session::setStatus(E::ts('The booking \'%1\' has been saved.', array(1 => $this->_values['title'])), E::ts('Saved'), 'success');
    }
  }

}

