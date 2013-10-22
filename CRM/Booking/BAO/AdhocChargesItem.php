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
    $params['name'] = str_replace(' ', '_', $params['label']);
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
    if ($item->find(TRUE)) {
      CRM_Core_DAO::storeValues($item, $defaults);
      return $item;
    }
    return NULL;
  }

  /**
   * update the is_active flag in the db
   *
   * @param int      $id        id of the database record
   * @param boolean  $is_active value we want to set the is_active field
   *
   * @return Object             DAO object on sucess, null otherwise
   * @static
   */
  static function setIsActive($id, $is_active) {
    return CRM_Core_DAO::setFieldValue('CRM_Booking_DAO_AdhocChargesItem', $id, 'is_active', $is_active);
  }


  /**
   * Function to delete adhoc charges item
   *
   * @param  int  $id     Id of the Adhoc Charges Item to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function del($id){
  	return CRM_Core_DAO::setFieldValue('CRM_Booking_DAO_AdhocChargesItem', $id, 'is_deleted', 1);
  }

}
