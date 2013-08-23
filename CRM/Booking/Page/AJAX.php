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



    $slots = array("data" => array(
                                    array("id" => 1,
                                          "start_date" =>  "2013-08-13 09:00:00",
                                          "end_date" =>"2013-08-13 12:00:00" ,
                                          "text" => "Task A-12458",
                                          "resource_id" => 2,
                                          "color" => "rgb(255,0,0)",
                                          "readonly" => true),

                                    array("id" => 2,
                                          "start_date" =>  "2013-08-13 09:00:00",
                                          "end_date" =>"2013-08-13 12:00:00" ,
                                          "text" => "Task A-12458",
                                          "resource_id" => 1,
                                          "color" => "rgb(255,0,0)",
                                          "readonly" => true),

                                  )
                );

    echo json_encode($slots);
    CRM_Utils_System::civiExit();

  }

  /**
   * Function to search resouce when adding resource to booking
   *
   */
  static function searchResource() {
    //dprint_r($_GET);
    //$resourceID  = CRM_Utils_Type::escape($_GET['resource_id'], 'Integer');
    $resourceType = CRM_Utils_Type::escape($_GET['type'], 'String');

    $params = array('resource_id' => $resourceID,
                    'resource_type' => $resourceType);

    $startDate = strtotime('today');
    $endDate =  strtotime('+1 week');

    //$timeRange = CRM_Booking_Utils_DateTime::createTimeRange('8:00', '22:30', '5 mins');
    $timeDisplayRange = CRM_Booking_Utils::createTimeRange('8:00', '17:00', '60 mins'); //for screen to display
    $totalPeriod = count($timeDisplayRange) + 1;
    //dprint_r($totalPeriod);
    $timeOptions = array();
      //$count = count($timeDisplayRange);
    foreach ($timeDisplayRange as $key => $time) {
      $timeOptions[$time]['time'] = date('G:i', $time);
      $timeOptions[$time]['width'] = (95/870)*30;

    }

    $searchResult = array();
    $resources = CRM_Booking_BAO_Resource::search($params);
    //check if the result is found.
    if(!empty($resources)){
      $periodInterval = new DateInterval( 'P1D' ); // 1-day, though can be more sophisticated rule
      $period = new DatePeriod( new DateTime(date('Y-m-d', $startDate)), $periodInterval, new DateTime(date('Y-m-d',$endDate )));
      foreach($period as $date){
          //FIXME - Use localisation for the date format
        $dayTable  =  array('date' => $date->format("d/m/Y"),
                            'date_timestamp' => strtotime($date->format('Y-m-d')),
                            'time_options' => $timeOptions
                           );
        $resourceRows = array();
        foreach ($resources as $key => $resource) {

          //TODO Get slot
          $slots = array(array('period_span' => $totalPeriod - 1,
                               'class' => ''
                               ));

          $resourceRow = array('id' => CRM_Utils_Array::value('id',$resource),
                              'label' => CRM_Utils_Array::value('label',$resource),
                              'description' => CRM_Utils_Array::value('description',$resource),
                              'resource_type' => CRM_Utils_Array::value('resource_type',$resource),
                              'resource_location' => CRM_Utils_Array::value('resource_location',$resource),
                              'is_unlimited' => CRM_Utils_Array::value('is_unlimited', $resource),
                              'slots' => $slots);

          array_push($resourceRows, $resourceRow);

        }
        $dayTable['resources'] = $resourceRows;
        $searchResult[] = array('result' => $dayTable);
      }
    }else{

    }


    echo json_encode($searchResult);
    CRM_Utils_System::civiExit();
  }


}

