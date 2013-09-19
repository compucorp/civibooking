<?php

class CRM_Booking_BAO_Cancellation extends CRM_Booking_DAO_Cancellation {


    /**
   * takes an associative array and creates a cancellation object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_Cancellation object
   * @access public
   * @static
   */
  static function create(&$params) {
    $dao = new CRM_Booking_DAO_Cancellation();
    $dao->copyValues($params);
    return $dao->save();
  }


}
