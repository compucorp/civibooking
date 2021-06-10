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
class CRM_Admin_Form_ResourceConfigOption extends CRM_Admin_Form {
  protected $_sid = NULL;

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(E::ts('Settings - Resource Configuration Option'));
    $this->_sid = CRM_Utils_Request::retrieve('sid', 'Positive',
      $this, FALSE, 0
    );
    $this->assign('sid', $this->_sid);

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

    $this->add('text', 'label', E::ts('Label'), array('size' => 50, 'maxlength' => 255), TRUE);
    $this->add('text', 'price', E::ts('Price'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'price '), TRUE);
    $this->add('text', 'max_size', E::ts('Max Size'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'max_size '), TRUE);
    $this->add('text', 'weight', E::ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'weight'), TRUE);
    $this->add('checkbox', 'is_active', E::ts('Enabled?'));

    $this->addRule("price", E::ts('Please enter a valid amount.'), 'money');

    $units =  CRM_Booking_BAO_ResourceConfigOption::buildOptions('unit_id', 'create');
    $this->add('select', 'unit_id', E::ts('Unit'),
      array('' => E::ts('- select -')) + $units,
      TRUE,
      array()
    );

    $this->addFormRule(array('CRM_Admin_Form_ResourceConfigOption', 'formRule'), $this);
    $cancelURL = CRM_Utils_System::url('civicrm/admin/resource/config_set/config_option', "&sid=$this->_sid&reset=1");
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
      $query = "SELECT max( `weight` ) as weight FROM `civicrm_booking_resource_config_option`";;
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
      CRM_Booking_BAO_ResourceConfigOption::del($this->_id);
      CRM_Core_Session::setStatus(E::ts('Selected resource configuration option has been deleted.'), E::ts('Record Deleted'), 'success');
    }
    else {
      $params = $this->exportValues();
      $params['set_id'] = $this->_sid;

      // If the is_active (enabled) checkbox is NOT set, it is NOT sent down in the form
      // The DAO definition for is_active has a default of '1'
      // So if not set it is by default ENABLED when in fact it should be DISABLED
      if(!isset($params['is_active'])){
        $params['is_active'] = 0;
      }

      if($this->_id){
        $params['id'] = $this->_id;
      }
      $resource = CRM_Booking_BAO_ResourceConfigOption::create($params);
      CRM_Core_Session::setStatus(E::ts('The Record \'%1\' has been saved.', array(1 => $resource->label)), E::ts('Saved'), 'success');

    }

    $url = CRM_Utils_System::url('civicrm/admin/resource/config_set/config_option', 'reset=1&action=browse&sid=' . $this->_sid);
    $session = CRM_Core_Session::singleton();
    $session->replaceUserContext($url);
  }




}
