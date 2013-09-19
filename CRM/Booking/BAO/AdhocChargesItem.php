<?php

class CRM_Booking_BAO_AdhocChargesItem extends CRM_Booking_DAO_AdhocChargesItem {


    /**
   * takes an associative array and creates a adhoc charges item object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_AdhocChargesItem object
   * @access public
   * @static
   */
  static function create(&$params) {
    $dao = new CRM_Booking_DAO_AdhocChargesItem();
    $dao->copyValues($params);
    return $dao->save();
  }

   /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
     * @return object CRM_Booking_DAO_AdhocChargesItem object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $item = new CRM_Booking_DAO_AdhocChargesItem();
    $item->copyValues($params);
    if ($dao->find(TRUE)) {
      CRM_Core_DAO::storeValues($item, $defaults);
      return $item;
    }
    return NULL;
  }



}
