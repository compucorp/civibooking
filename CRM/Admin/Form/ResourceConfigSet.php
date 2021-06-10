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
 * This class generates form components for Resource
 *
 */
class CRM_Admin_Form_ResourceConfigSet extends CRM_Admin_Form {

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(E::ts('Settings - Resource Configuration Set'));
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

    $this->add('text', 'title', E::ts('Title'), array('size' => 50, 'maxlength' => 255), TRUE);
    $this->add('text', 'weight', E::ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigSet', 'weight'), TRUE);
    $statusCheckbox = $this->add('checkbox', 'is_active', E::ts('Enabled?'));
    
    //allow state changes and delete only when there are no enabled resources
    $resourceDao = new CRM_Booking_DAO_Resource();
    $resourceDao->set_id = $this->_id;
    $resourceDao->is_deleted = FALSE;
    $resourceDao->is_active = TRUE;
    
    if($resourceDao->count() > 0){
      $statusCheckbox->setAttribute('disabled', 'disabled');
    }


    $this->addFormRule(array('CRM_Admin_Form_Resource', 'formRule'), $this);
    $cancelURL = CRM_Utils_System::url('civicrm/admin/resource/config_set', "&reset=1");
    $cancelURL = str_replace('&amp;', '&', $cancelURL);
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
    if (!empty($errors)) {
      return $errors;
    }

    return empty($errors) ? TRUE : $errors;
  }



  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if (!CRM_Utils_Array::value('weight', $defaults)) {
      $query = "SELECT max( `weight` ) as weight FROM `civicrm_booking_resource_config_set`";;
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
    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Booking_BAO_ResourceConfigSet::del($this->_id);
      CRM_Core_Session::setStatus(E::ts('Selected resource configuration set has been deleted.'), E::ts('Record Deleted'), 'success');
    }
    else {
      $params = $this->exportValues();

      // If the is_active (enabled) checkbox is NOT set, it is NOT sent down in the form
      // The DAO definition for is_active has a default of '1'
      // So if not set it is by default ENABLED when in fact it should be DISABLED
      if(!isset($params['is_active'])){
        $params['is_active'] = 0;
      }

      if($this->_id){
        $params['id'] = $this->_id;
      }
      $set = CRM_Booking_BAO_ResourceConfigSet::create($params);

      if ($this->_action & CRM_Core_Action::UPDATE) {
        CRM_Core_Session::setStatus(E::ts('The Record \'%1\' has been saved.', array(1 => $set->title)), E::ts('Saved'), 'success');
      }
      else {
        $url = CRM_Utils_System::url('civicrm/admin/resource/config_set/config_option', 'reset=1&action=add&sid=' . $set->id);
        CRM_Core_Session::setStatus(
          E::ts("Your resource configuration set '%1' has been added. You can add resource option now.", array(1 => $set->title)), E::ts('Saved'), 'success');
        $session = CRM_Core_Session::singleton();
        $session->replaceUserContext($url);
      }
    }
  }



}
