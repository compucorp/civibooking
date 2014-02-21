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
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive',
      $this, FALSE, 0
    );
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
    $this->add('select', 'type_id', ts('Resource type'),
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
    $this->add('checkbox', 'is_unlimited', ts('Is Unlimited?'));


    $configSets =  array('' => ts('- select -'));
    try{
      $activeSets = civicrm_api3('ResourceConfigSet', 'get', array('is_active' => 1, 'is_deleted' => 0));
      foreach ($activeSets['values'] as $key => $set) {
        $configSets[$key] = $set['title'];
      }
    }
    catch (CiviCRM_API3_Exception $e) {}

    $this->add('select', 'set_id', ts('Resource configuration set'), $configSets, TRUE);

    $locations =  CRM_Booking_BAO_Resource::buildOptions('location_id', 'create');
    $this->add('select', 'location_id', ts('Resource Location'),
      array('' => ts('- select -')) + $locations,
      FALSE,
      array()
    );

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
   $errors = array();
   $setId =  CRM_Utils_Array::value('set_id', $fields);
   if($setId){
     try{
        $options = civicrm_api3('ResourceConfigOption', 'get', array('set_id' => $setId));
        $count = CRM_Utils_Array::value('count', $options);
        if($count == 0){
          $errors['set_id'] = ts('The selected set does not contain any options, please select another');
        }
      }
      catch (CiviCRM_API3_Exception $e) {}
   }
    return empty($errors) ? TRUE : $errors;
  }



  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if (!CRM_Utils_Array::value('weight', $defaults)) {
      $query = "SELECT max( `weight` ) as weight FROM `civicrm_booking_resource`";;
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

      CRM_Booking_BAO_Slot::delByResource($this->_id);

      CRM_Booking_BAO_Resource::del($this->_id);
      CRM_Core_Session::setStatus(ts('Selected resource has been deleted.'), ts('Record Deleted'), 'success');

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


      $resource = CRM_Booking_BAO_Resource::create($params);
      CRM_Core_Session::setStatus(ts('The Record \'%1\' has been saved.', array(1 => $resource->label)), ts('Saved'), 'success');
    }
  }



}
