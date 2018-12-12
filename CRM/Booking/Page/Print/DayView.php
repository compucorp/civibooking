<?php

require_once 'CRM/Core/Page.php';

class CRM_Booking_Page_Print_DayView extends CRM_Core_Page {

    function run() {
        //get date value from url through get method
        $date = CRM_Utils_Request::retrieve('date', 'Positive', $this, FALSE, 0);

        //convert javascript.getTime() to PHP date format
        $date =  date('m/d/Y', round($date/1000));

        //get resources information by selected date
        $from = date('Y-m-d', strtotime($date));
        $to = date('Y-m-d', strtotime($from . ' +1 day'));
        $resources = CRM_Booking_BAO_Slot::getSlotDetailsOrderByResourceBetweenDate($from, $to);

        $values = array();
        //put resources result to values, being ready to display.
        $values['resources'] = $resources;
        //Convert date to compile with crmDate
        $values['dayview_select_date'] = DateTime::createFromFormat('m/d/Y',$date)->format('Y-m-d');

        //assign variables for use in a template
        $this -> assign($values);

        // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
        CRM_Utils_System::setTitle(E::ts('DayViewPrint'));

        parent::run();
    }

}
