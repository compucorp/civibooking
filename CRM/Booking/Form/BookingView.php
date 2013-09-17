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
class CRM_Booking_Form_BookingView extends CRM_Core_Form {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $values        = $ids = array();
    $bookingID = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    $contactID     = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $params        = array('id' => $bookingID);


    CRM_Booking_BAO_Booking::getValues($params,
      $values,
      $ids
    );


    if (empty($values)) {
      CRM_Core_Error::statusBounce(ts('The requested booking record does not exist (possibly the record was deleted).'));
    }

    //TODO:: Implement lresolveDefaults, see how participant works
    //CRM_Booking_BAO_Booking::resolveDefaults($values[$bookingID]);
    //GET Slot
    $slots = CRM_Booking_BAO_Slot::getBookingSlot($bookingID);
    foreach ($slots as $key => $slot) {
      $slots[$key]['sub_slots'] = CRM_Booking_BAO_SubSlot::getSubSlotSlot($key);
    }

    $values[$bookingID]['slots'] = $slots;

    $this->assign($values[$bookingID]);

    $displayName = CRM_Contact_BAO_Contact::displayName($values[$bookingID]['primary_contact_id']);

    $this->assign('displayName', $displayName);
    // omitting contactImage from title for now since the summary overlay css doesn't work outside of our crm-container
    CRM_Utils_System::setTitle(ts('View Booking for') .  ' ' . $displayName);

  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $this->addButtons(array(
        array(
          'type' => 'cancel',
          'name' => ts('Done'),
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'isDefault' => TRUE,
        ),
      )
    );
  }
}

