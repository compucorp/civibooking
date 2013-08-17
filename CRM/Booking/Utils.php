<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
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

class CRM_Booking_Utils {


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




}
