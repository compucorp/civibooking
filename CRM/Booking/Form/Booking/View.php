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
    $subSlots = array();
    foreach ($slots as $key => $slot) {
      $label =  CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_Resource',
        $slot['resource_id'],
        'label',
        'id'
      );
      //Quite expensive
      $slots[$key]['resource_label'] = $label;
      $slots[$key]['config_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
        $slot['config_id'],
        'label',
        'id'
      );
      $params = array(
          'version' => 3,
          'entity_id' => $slot['id'],
          'entity_table' => 'civicrm_booking_slot',
        );
      $result = civicrm_api('LineItem', 'get', $params);
      if(!empty($result['values'])){
        $lineItem = CRM_Utils_Array::value($result['id'], $result['values']);
        $slots[$key]['unit_price'] = CRM_Utils_Array::value('unit_price', $lineItem);
        $slots[$key]['total_amount'] = CRM_Utils_Array::value('line_total', $lineItem);
        $slots['quantity'] = CRM_Utils_Array::value('qty', $lineItem);
      }else{ //calulate manuanlly
        $slots[$key]['total_amount'] = CRM_Booking_BAO_Booking::calulateSlotPrice($slot['config_id'], $slot['quantity']);
      }
      $childSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($key);
      foreach ($childSlots as $key => $subSlot) {
        $subSlot['resource_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_Resource',
          $subSlot['resource_id'],
          'label',
          'id'
        );
        $subSlot['config_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
          $subSlot['config_id'],
          'label',
          'id'
        );
        $params = array(
          'version' => 3,
          'entity_id' => $subSlot['id'],
          'entity_table' => 'civicrm_booking_sub_slot',
        );
        $result = civicrm_api('LineItem', 'get', $params);
        if(!empty($result['values'])){
          $subSlotlineItem = CRM_Utils_Array::value($result['id'], $result['values']);
          $subSlot['unit_price'] = CRM_Utils_Array::value('unit_price', $subSlotlineItem);
          $subSlot['total_amount'] = CRM_Utils_Array::value('line_total', $subSlotlineItem);
          $subSlot['quantity'] = CRM_Utils_Array::value('qty', $subSlotlineItem);
        }else{ //calulate manuanlly
          $subSlot['total_amount'] = CRM_Booking_BAO_Booking::calulateSlotPrice($subSlot['config_id'], $subSlot['quantity']);
        }

        $subSlot['parent_resource_label'] =  $label;
        $subSlots[$subSlot['id']] = $subSlot;
      }

    }

    $this->_values['slots'] = $slots;
    $this->_values['sub_slots'] = $subSlots;

    $this->_values['sub_total'] = CRM_Utils_Array::value('total_amount', $this->_values) + CRM_Utils_Array::value('discount_amount', $this->_values);

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

