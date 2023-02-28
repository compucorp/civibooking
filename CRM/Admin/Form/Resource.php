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
class CRM_Admin_Form_Resource extends CRM_Admin_Form {

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(E::ts('Settings - Resource'));
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
    $this->add('select', 'type_id', E::ts('Resource type'),
      array('' => E::ts('- select -')) + $types,
      TRUE,
      array()
    );

    $this->add('text', 'label', E::ts('Label'), array('size' => 50, 'maxlength' => 255), TRUE);
    $this->add('textarea', 'description', E::ts('Description'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'description'), FALSE);
    /*
    $this->addWysiwyg('description',
        E::ts('Description'),
        CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'description')
    );*/

    $this->add('text', 'weight', E::ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Booking_DAO_Resource', 'weight'), TRUE);
    $statusCheckbox = $this->add('advcheckbox', 'is_active', E::ts('Enabled?'));
    $this->add('advcheckbox', 'is_unlimited', E::ts('Is Unlimited?'));


    $configSets =  array('' => E::ts('- select -'));
    try{
      $activeSets = civicrm_api3('ResourceConfigSet', 'get', array('is_active' => 1, 'is_deleted' => 0));
      foreach ($activeSets['values'] as $key => $set) {
        $configSets[$key] = $set['title'];
      }
      
      $resource = civicrm_api3('Resource', 'getsingle', array(
        'sequential' => 1,
        'id' => $this->_id,
      ));
    }
    catch (CiviCRM_API3_Exception $e) {}   

    //allow state changes only when there is enabled config set
    if(!empty($resource['set_id']) && !in_array($resource['set_id'], array_keys($activeSets['values']))){
      $statusCheckbox->setAttribute('disabled', 'disabled');
    }

    $this->add('select', 'set_id', E::ts('Resource configuration set'), $configSets, TRUE);

    $locations =  CRM_Booking_BAO_Resource::buildOptions('location_id', 'create');
    $this->add('select', 'location_id', E::ts('Resource Location'),
      array('' => E::ts('- select -')) + $locations,
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
   $errors = array();
   $setId =  CRM_Utils_Array::value('set_id', $fields);
   if($setId){
     try{
        $options = civicrm_api3('ResourceConfigOption', 'get', array('set_id' => $setId));
        $count = CRM_Utils_Array::value('count', $options);
        if($count == 0){
          $errors['set_id'] = E::ts('The selected set does not contain any options, please select another');
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
      CRM_Core_Session::setStatus(E::ts('Selected resource has been deleted.'), E::ts('Record Deleted'), 'success');

    }
    else {
      if($this->_id){
        $params['id'] = $this->_id;
      }


      $resource = CRM_Booking_BAO_Resource::create($params);
      CRM_Core_Session::setStatus(E::ts('The Record \'%1\' has been saved.', array(1 => $resource->label)), E::ts('Saved'), 'success');
    }
  }



}
