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
 * This class generates form components for Booking
 *
 */
class CRM_Booking_Form_Booking_View extends CRM_Booking_Form_Booking_Base {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    parent::preProcess();

    $slots = CRM_Booking_BAO_Slot::getBookingSlot($this->_id);
    foreach ($slots as $key => $slot) {
      //Quite expensive
      $slots[$key]['resource_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_Resource',
        $slot['resource_id'],
        'label',
        'id'
      );
      $slots[$key]['config_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
        $slot['config_id'],
        'label',
        'id'
      );
      $subSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($key);
      foreach ($subSlots as $key => $subSlot) {
        $subSlots[$key]['resource_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_Resource',
          $subSlot['resource_id'],
          'label',
          'id'
        );
        $subSlots[$key]['config_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
          $subSlot['config_id'],
          'label',
          'id'
        );
      }

      $slots[$key]['sub_slots'] = $subSlots;

    }

    $this->_values['slots'] = $slots;

    $this->assign($this->_values);

    $displayName = CRM_Contact_BAO_Contact::displayName($this->_values['primary_contact_id']);
    $secondaryContactDisplayName = CRM_Contact_BAO_Contact::displayName($this->_values['secondary_contact_id']);

    $this->assign('displayName', $displayName);
    $this->assign('secondaryContactDisplayName',$secondaryContactDisplayName );
    $this->assign('contact_id', $this->_cid);
    // omitting contactImage from title for now since the summary overlay css doesn't work outside of our crm-container
    CRM_Utils_System::setTitle(ts('View Booking for') .  ' ' . $displayName);


  }

}

