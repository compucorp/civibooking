<?php

class CRM_Booking_BAO_SubSlot extends CRM_Booking_DAO_SubSlot {


    /**
   * takes an associative array and creates a sub slot object
   *
   * the function extract all the params it needs to initialize the create a
   * sub slot object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_SubSlot object
   * @access public
   * @static
   */
  static function create(&$params) {
    $subSlotDAO = new CRM_Booking_DAO_SubSlot();
    $subSlotDAO->copyValues($params);
    return $subSlotDAO->save();
  }


}
