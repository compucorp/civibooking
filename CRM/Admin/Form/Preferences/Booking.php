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
class CRM_Admin_Form_Preferences_Booking extends CRM_Core_Form {
  protected $_id = NULL;

  function preProcess() {
    parent::preProcess();
    CRM_Utils_System::setTitle(ts('Settings - Booking Perferences Configuration'));
    self::registerScripts();

  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm($check = FALSE) {
    parent::buildQuickForm();

    $financialType = CRM_Contribute_PseudoConstant::financialType();
    $this->add('select', 'financial_type_default', ts('Default Financial type'),
      array('' => ts('- select -')) + $financialType,
      TRUE,
      array()
    );

    $priceSets = array(); //TODO Get list of price set
    $this->add('select', 'price_set_default', ts('Default price set'),
      array('' => ts('- select -')) + $priceSets,
      FALSE,
      array()
    );

    $timeRange =  CRM_Booking_Utils_DateTime::createTimeRange("00:00", "24:00");
    $timeOptions = array();
    foreach ($timeRange as $key => $time) {
      $option = date('G:i', $time);
      $timeOptions[$option] = $option;
    }

    $this->add('select', 'day_start_at', ts('Day starts at'),
      $timeOptions ,
      FALSE,
      array()
    );

    $this->add('select', 'day_end_at', ts('Day ends at'),
      $timeOptions,
      FALSE,
      array()
    );

    $this->add('text', 'selected_email_address', ts('Select from emails'), array('size' => 50, 'maxlength' => 255), FALSE);
    $this->add('text', 'cc_email_address', ts('CC'), array('size' => 50, 'maxlength' => 255), FALSE);
    $this->add('text', 'bcc_email_address', ts('BCC'), array('size' => 50, 'maxlength' => 255), FALSE);



    $this->add('checkbox', 'created_activity', ts('Is Unlimited?'));

    $this->add('text', 'slot_avaliable_colour', ts('Slot avaliable colour'));
    $this->add('text', 'slot_unavaliable_colour', ts('Slot unavaliable colour'));
    //$this->add('text', 'slot_owner_colour', ts('Slot unavaliable colour'));


    $this->addFormRule(array('CRM_Admin_Form_Preferences_Booking', 'formRule'), $this);

    $this->addButtons(
      array(
        array(
          'type' => 'next',
          'name' => ts('Save'),
          'isDefault' => TRUE,
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
   * @return None
   */
  public function postProcess() {
    CRM_Utils_System::flushCache();

  }


  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()
      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/booking.css', 92, 'page-header');
  }


}
