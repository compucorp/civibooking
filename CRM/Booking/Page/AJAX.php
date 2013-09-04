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
 * This class contains all the function that are called using AJAX
 */
class CRM_Booking_Page_AJAX {

  /**
  * Custom getContactList AJAX  from CRM_Contact_Page_AJAX
  *
  */
  static function getContactList() {


    $params = array('version' => 3, 'check_permissions' => TRUE);

    // String params
    // FIXME: param keys don't match input keys, using this array to translate
    $whitelist = array(
      's' => 'name',
      'fieldName' => 'field_name',
      'tableName' => 'table_name',
      'context' => 'context',
      'rel' => 'rel'
    );
    foreach ($whitelist as $key => $param) {
      if (!empty($_GET[$key])) {
        $params[$param] = $_GET[$key];
      }
    }

    //CRM-10687: Allow quicksearch by multiple fields
    if (!empty($params['field_name'])) {
      if ($params['field_name'] == 'phone_numeric') {
        $params['name'] = preg_replace('/[^\d]/', '', $params['name']);
      }
      if (!$params['name']) {
        CRM_Utils_System::civiExit();
      }
    }

    // Numeric params
    $whitelist = array(
      'limit',
      'org',
      'employee_id',
      'cid',
      'id',
      'cmsuser',
    );
    foreach ($whitelist as $key) {
      if (!empty($_GET[$key]) && is_numeric($_GET[$key])) {
        $params[$key] = $_GET[$key];
      }
    }

    $result = civicrm_api('Contact', 'getquick', $params);
    if (empty($result['is_error']) && !empty($result['values'])) {
      foreach ($result['values'] as $key => $val) {
        echo "{$val['id']}::{$val['data']}|{$val['id']}\n";
      }
    }
    CRM_Utils_System::civiExit();
  }



  static function getSlots(){

    $timeshift = CRM_Utils_Type::escape($_GET['timeshift'], 'String');
    $from = CRM_Utils_Type::escape($_GET['from'], 'String');
    $to = CRM_Utils_Type::escape($_GET['to'], 'String');


    $slots = array("data" => array());
    $results = CRM_Booking_BAO_Slot::getSlotBetweenDate($from, $to);
    foreach ($results as $key => $slot) {
     array_push($slots['data'],
      array(
        "id" => $key,
        "start_date" => CRM_Utils_Array::value('start', $slot),
        "end_date" =>CRM_Utils_Array::value('end', $slot),
        "text" => CRM_Utils_Array::value('note', $slot),
        "resource_id" => CRM_Utils_Array::value('resource_id', $slot),
        "color" => "rgb(255,0,0)",
        "readonly" => true));
    }

    echo json_encode($slots);
    CRM_Utils_System::civiExit();

  }


}

