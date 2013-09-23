<?php

class CRM_Booking_BAO_BookingConfig extends CRM_Booking_DAO_BookingConfig {


    /**
   * takes an associative array and creates a resource object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_Resource object
   * @access public
   * @static
   */
  static function create(&$params) {
    if(!CRM_Utils_Array::value('id', $params)){
      //make sure we don't create new record
      return;
    }
    $dao = new CRM_Booking_DAO_BookingConfig();
    $dao->copyValues($params);
    return $dao->save();
  }

  static function getConfig(){
    $dao = new CRM_Booking_DAO_BookingConfig();
    $dao->find();
    $config = array();
    while ($dao->fetch()) {
      CRM_Core_DAO::storeValues($dao, $config);
      return $config;
    }
  }


}
