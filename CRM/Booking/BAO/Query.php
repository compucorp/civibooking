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
class CRM_Booking_BAO_Query {

  static function &getFields() {
    $fields = array();
    $fields = array_merge($fields, CRM_Booking_DAO_Booking::import());
    return $fields;
  }

  /**
   * build select for CiviBooking
   *
   * @return void
   * @access public
   */
  static function select(&$query) {

    if (($query->_mode & CRM_Booking_BAO_BookingContactQuery::MODE_BOOKING) ||
      CRM_Contact_BAO_Query::componentPresent($query->_returnProperties, 'booking_')) {

      $query->_select['booking_id'] = "civicrm_booking.id as booking_id";
      $query->_element['booking_id'] = 1;
      $query->_tables['civicrm_booking'] = $query->_whereTables['civicrm_booking'] = 1;

      if (CRM_Utils_Array::value('booking_po_no', $query->_returnProperties)) {
        $query->_select['booking_po_no'] = "civicrm_booking.po_number as booking_po_no";
        $query->_element['booking_po_no'] = 1;
      }

           //add status and status_id
      if (CRM_Utils_Array::value('booking_status', $query->_returnProperties) ||
        CRM_Utils_Array::value('booking_status_id', $query->_returnProperties)
      ) {
        $query->_select['booking_status'] = "booking_status.label as booking_status";
        $query->_select['booking_status_id'] = "booking_status.id as booking_status_id";
        $query->_element['booking_status_id'] = 1;
        $query->_element['booking_status'] = 1;
        $query->_tables['civicrm_booking'] = 1;
        $query->_tables['booking_status'] = 1;
        $query->_whereTables['civicrm_booking'] = 1;
        $query->_whereTables['booking_status'] = 1;
      }


      if (CRM_Utils_Array::value('booking_title', $query->_returnProperties)) {
        $query->_select['booking_title'] = "civicrm_booking.title as booking_title";
        $query->_element['booking_title'] = 1;
      }

    }
  }

  static function where(&$query) {
    $grouping = NULL;
    foreach (array_keys($query->_params) as $id) {
      if (!CRM_Utils_Array::value(0, $query->_params[$id])) {
        continue;
      }
    }
  }

  static function whereClauseSingle(&$values, &$query) {

    list($name, $op, $value, $grouping, $wildcard) = $values;
    switch ($name) {
      case 'title':
        $query->_where[$grouping][] = CRM_Booking_BAO_BookingContactQuery::buildClause("civcirm_booking.title", $op, $value, 'String');
        $query->_qill[$grouping][] = ts("Booking title %1 '%2'", array(1 => $op, 2 => $value));
        $query->_tables['civicrm_booking'] = $query->_whereTables['civicrm_booking'] = 1;
        return;
    }
  }

  static function from($name, $mode, $side) {
    $from = NULL;

    switch ($name) {
      case 'civicrm_booking':
        $from = " $side JOIN civicrm_booking ON civicrm_booking.primary_contact_id = contact_a.id ";
        break;

      case 'booking_status':
        $from = " $side JOIN civicrm_option_group option_group_booking_status ON (option_group_booking_status.name = 'booking_booking_status')";
        $from .= " $side JOIN civicrm_option_value booking_status ON (civicrm_booking.status_id = booking_status.value AND option_group_booking_status.id = booking_status.option_group_id ) ";
        break;
      //TODO JOIN booking payment
      case 'booking_payment':
        $from .= " ";
        break;

    }
    return $from;
  }

  /**
   * getter for the qill object
   *
   * @return string
   * @access public
   */
  function qill() {
    return (isset($this->_qill)) ? $this->_qill : "";
  }

  static function defaultReturnProperties($mode, $includeCustomFields = TRUE) {
    $properties = NULL;
    if ($mode & CRM_Booking_BAO_BookingContactQuery::MODE_BOOKING) {
      $properties = array(
        'contact_type' => 1,
        'contact_sub_type' => 1,
        'sort_name' => 1,
        'display_name' => 1,
        'booking_po_no' => 1,
        'booking_status_id' => 1,
        'booking_title' => 1,
        'booking_discount_amount' => 1,
        'booking_payment_status' => 1,
      );
    }

    return $properties;
  }

  static function buildSearchForm(&$form) {


    $form->add('text', 'po_no', ts('Purchase order number'));

    $resourceTypes =  CRM_Booking_BAO_Resource::getResourceTypes();
    $resources = array();
    foreach ($resourceTypes as $value) {
      $resources[$value['id']] = $value['label'];
    }
    $form->add('select', 'resource_id', ts('Resources'),
      array('' => ts('- select -')) + $resources,
      FALSE,
      array()
    );

    $form->add('text', 'id', ts('Booking ID'));
    $form->add('text', 'title', ts('Booking Title'));

    CRM_Core_Form_Date::buildDateRange($form, 'event_start_date', 1, '_start_date_low', '_end_date_high', ts('From'), FALSE);

    $bookingStatus =  CRM_Booking_BAO_Booking::buildOptions('status_id', 'create');
    foreach ($bookingStatus as $id => $name) {
      $form->_bookingStatus = &$form->addElement('checkbox', "booking_status_id[$id]", NULL, $name);
    }

    $paymentStatus = CRM_Contribute_PseudoConstant::contributionStatus();
    foreach ($paymentStatus as $id => $name) {
      $form->_paymentStatus = &$form->addElement('checkbox', "payment_status_id[$id]", NULL, $name);
    }

  }

  static function searchAction(&$row, $id) {

  }


}

