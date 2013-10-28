<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                             |
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
 * @package CDM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

class CRM_Booking_Utils_DateTime {


    /**
   * create_time_range
   *
   * @param mixed $start start time, e.g., 9:30am or 9:30
   * @param mixed $end   end time, e.g., 5:30pm or 17:30
   * @param string $by   1 hour, 1 mins, 1 secs, etc.
   * @access public
   * @return void
   */
  static function createTimeRange($start, $end, $by='30 mins') {

      $start_time = strtotime($start);
      $end_time   = strtotime($end);

      $current    = time();
      $add_time   = strtotime('+'.$by, $current);
      $diff       = $add_time-$current;

      $times = array();
      while ($start_time < $end_time) {
          $times[] = $start_time;
          $start_time += $diff;
      }
      $times[] = $start_time;
      return $times;
  }

  static function getTimeRange(){
    //FIXED ME, get start and end time from the configuration
    $timeRange = self::createTimeRange('8:00', '22:30', '5 mins');
    $timeOptions = array();
    foreach ($timeRange as $key => $time) {
      $timeOptions[$time]['time'] = date('G:i', $time);
    }
    return $timeOptions;

  }

  static function getYears(){
    $yearRange = range(date("Y" ,strtotime("now - 4 years")), date("Y", strtotime("now + 4 years")));
    $years = array();
    foreach ($yearRange as  $year) {
      $years[$year] = $year;
    }
    return $years;
  }

  static function getDays(){
    return range(1, 31);
  }

  static function getCalendarTime(){
    $config = CRM_Booking_BAO_BookingConfig::getConfig();
    $start = strtotime(CRM_Utils_Array::value('day_start_at', $config));
    $end = strtotime(CRM_Utils_Array::value('day_end_at', $config));
    $periodTime = 30; //fixed the period time
    $startHour = 8;
    $startMinutes = 30;
    $endHour = 22;
    $endMinutes = 30;
    $xStart = (($startHour * (60 / $startMinutes)) + ($startMinutes / $periodTime));
    $xSize =  (($endHour * (60 / $periodTime)) + ($endMinutes / $periodTime)) - ((($startHour * (60 / $periodTime)) + ($startMinutes / $periodTime )) - 1);
    return array($xStart, $xSize, $periodTime);

  }

}
