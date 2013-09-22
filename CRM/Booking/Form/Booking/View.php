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

    //TODO:: Implement lresolveDefaults, see how participant works
    //CRM_Booking_BAO_Booking::resolveDefaults($values[$bookingID]);
    //GET Slot
    $slots = CRM_Booking_BAO_Slot::getBookingSlot($this->_id);
    foreach ($slots as $key => $slot) {
      $slots[$key]['sub_slots'] = CRM_Booking_BAO_SubSlot::getSubSlotSlot($key);
    }

    $this->_values[$this->_id]['slots'] = $slots;

    $this->assign($this->_values[$this->_id]);

    $displayName = CRM_Contact_BAO_Contact::displayName($this->_values[$this->_id]['primary_contact_id']);

    $this->assign('displayName', $displayName);
    $this->assign('contact_id', $this->_cid);
    // omitting contactImage from title for now since the summary overlay css doesn't work outside of our crm-container
    CRM_Utils_System::setTitle(ts('View Booking for') .  ' ' . $displayName);


  }

}

