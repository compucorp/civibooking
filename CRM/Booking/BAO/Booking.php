<?php

class CRM_Booking_BAO_Booking extends CRM_Booking_DAO_Booking {


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
    $resourceDAO = new CRM_Booking_DAO_Booking();
    $resourceDAO->copyValues($params);
    return $resourceDAO->save();
  }


}
