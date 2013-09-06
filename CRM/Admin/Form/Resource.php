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
class CRM_Admin_Form_Resource extends CRM_Admin_Form {
  protected $_id = NULL;

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(ts('Settings - Resource'));

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


    $types =  CRM_Booking_BAO_Resource::buildOptions('type_id', 'create');
    $this->add('select', 'type_id', ts('Resoruce type'),
      array('' => ts('- select -')) + $types,
      TRUE,
      array()
    );

    $this->add('text', 'label', ts('Label'), array('size' => 50, 'maxlength' => 255), TRUE);
    $this->add('textarea', 'description', ts('Description'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'description'), FALSE);
    /*
    $this->addWysiwyg('description',
        ts('Description'),
        CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'description')
    );*/

    $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'weight'), TRUE);
    $this->add('checkbox', 'is_active', ts('Enabled?'));
    $this->add('checkbox', 'is_unlimited', ts('Is Unlimited?'),CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'is_unlimited'), TRUE);


    $configSets =  array('' => ts('- select -'));
    $activeSets = CRM_Booking_BAO_ResourceConfigSet::getActiveSet();
    foreach ($activeSets as $key => $set) {
      $configSets[$key] = $set['title'];
    }
    $this->add('select', 'set_id', ts('Resource configuration set'), $configSets, TRUE);

    $locations =  CRM_Booking_BAO_Resource::buildOptions('location_id', 'create');
    $this->add('select', 'location_id', ts('Resoruce Location'),
      array('' => ts('- select -')) + $locations,
      FALSE,
      array()
    );

    /*
    if ($this->_action & CRM_Core_Action::UPDATE && $isReserved) {
      $this->freeze(array('name', 'description', 'is_active'));
    }*/

    $this->addFormRule(array('CRM_Admin_Form_Resource', 'formRule'), $this);
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
    $defaults = parent::setDefaultValues();
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
