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
 *
 */

/**
 * This class generates form components for AdhocChargesItem
 *
 */
class CRM_Admin_Form_AdhocChargesItem extends CRM_Admin_Form {

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(E::ts('Settings - Additional Charges Item'));
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm($check = FALSE) {
    parent::buildQuickForm();

    if ($this->_action & CRM_Core_Action::DELETE) {
      return;
    }

	  // create form elements
    $this->add('text', 'label', E::ts('Label'), array('size' => 50, 'maxlength' => 255), TRUE);
    $this->add('text', 'price', E::ts('Price'), array('size' => 10, 'maxlength' => 255), TRUE);
    $this->add('text', 'weight', E::ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_AdhocChargesItem', 'weight'), TRUE);
    $this->add('checkbox', 'is_active', E::ts('Enabled?'));

    $this->addRule("price", E::ts('Please enter a valid amount.'), 'money');

	// add form rule
    $this->addFormRule(array('CRM_Admin_Form_AdhocChargesItem', 'formRule'), $this);
    $cancelURL = CRM_Utils_System::url('civicrm/admin/adhoc_charges_item', "&reset=1");
    $cancelURL = str_replace('&amp;', '&', $cancelURL);

    // add button
    $this->addButtons(
      array(
        array(
          'type' => 'next',
          'name' => E::ts('Save'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'cancel',
          'name' => E::ts('Cancel'),
          'js' => array('onclick' => "location.href='{$cancelURL}'; return false;"),
        ),
      )
    );
  }


  static function formRule($fields) {
  	// get price value
	  $price = CRM_Utils_Array::value('price', $fields);
	  // put validation
	  if(!is_numeric($price)){
	   // set error msg
		  $errors['price'] = E::ts('This field should be numeric.');
	  }
    if (!empty($errors)) {
      return $errors;
    }
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if (!CRM_Utils_Array::value('weight', $defaults)) {
      $query = "SELECT max( `weight` ) as weight FROM `civicrm_booking_adhoc_charges_item`";;
      $dao = new CRM_Core_DAO();
      $dao->query($query);
      $dao->fetch();
      $defaults['weight'] = ($dao->weight + 1);
    }
    return $defaults;
  }


  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
   public function postProcess() {
    CRM_Utils_System::flushCache();
    $params = $this->exportValues();

    // delete action
    // TODO::Make sure we cannot delete if the entity is linked to bookings
    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Booking_BAO_AdhocChargesItem::del($this->_id);
      CRM_Core_Session::setStatus(E::ts('Selected additional charges item has been deleted.'), E::ts('Record Deleted'), 'success');
    }
    else {
      $params = $this->exportValues();
      if($this->_id){
        $params['id'] = $this->_id;
        if(!isset($params['is_active'])){
          $params['is_active'] = 0;
        }
      }
      $set = CRM_Booking_BAO_AdhocChargesItem::create($params);

		// udpate action
      if ($this->_action & CRM_Core_Action::UPDATE) {
        CRM_Core_Session::setStatus(E::ts('The Record \'%1\' has been saved.', array(1 => $set->label)), E::ts('Saved'), 'success');
      }
      else {
        $url = CRM_Utils_System::url('civicrm/admin/adhoc_charges_item', 'reset=1&action=browse&sid=' . $set->id);
        CRM_Core_Session::setStatus(
          E::ts("Your additional charges item '%1' has been added.", array(1 => $set->label)), E::ts('Saved'), 'success');
        $session = CRM_Core_Session::singleton();
        $session->replaceUserContext($url);
      }
    }
  }

}
