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
 *
 */

/**
 * This class generates form components for Resource
 *
 */
class CRM_Admin_Form_ResourceConfigOption extends CRM_Admin_Form {
  protected $_id = NULL;

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(ts('Settings - Resource Configuration Option'));

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

    $this->add('text', 'name', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'label '), TRUE);
    $this->add('text', 'price', ts('Price'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'price '), TRUE);
    $this->add('text', 'max_size', ts('Max Size'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'max_size '), TRUE);
    $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_ResourceConfigOption', 'weight'), TRUE);
    $this->add('checkbox', 'is_active', ts('Enabled?'));


    $units =  CRM_Booking_BAO_ResourceConfigOption::buildOptions('unit_id', 'create');
    $this->add('select', 'unit_id', ts('Unit'),
      array('' => ts('- select -')) + $units,
      TRUE,
      array()
    );

    $this->addFormRule(array('CRM_Admin_Form_ResourceConfigOption', 'formRule'), $this);
    $cancelURL = CRM_Utils_System::url('civicrm/admin/resource', "&reset=1");
    $cancelURL = str_replace('&amp;', '&', $cancelURL);
    $this->addButtons(
      array(
        array(
          'type' => 'next',
          'name' => ts('Save'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
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
    $defaults = array();

    return $defaults;
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return Void
   */
  public function postProcess() {

  }


}
