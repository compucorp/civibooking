<?php

class CRM_Booking_BAO_ResourceConfigOption extends CRM_Booking_DAO_ResourceConfigOption {


    /**
   * takes an associative array and creates a resource config option= object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_ResourceConfigOption object
   * @access public
   * @static
   */
  static function create(&$params) {
    $configOption = new CRM_Booking_DAO_ResourceConfigOption();
    $configOption->copyValues($params);
    return $configOption->save();
  }
      /**
   * Function to delete ResourceConfigOption
   *
   * @param  int  $id     Id of the Resource Config Option to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function del($id) {
    $configOption = new CRM_Booking_DAO_ResourceConfigOption();
    $configOption->id = $id;
    return $configOption->delete();
  }



    /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
     * @return object CRM_Booking_DAO_ResourceConfigOtpion object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $configOption = new CRM_Booking_DAO_ResourceConfigOption();
    $configOption->copyValues($params);
    if ($configOption->find(TRUE)) {
      CRM_Core_DAO::storeValues($configOption, $defaults);
      return $configOption;
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
    return CRM_Core_DAO::setFieldValue('CRM_Booking_DAO_ResourceConfigOption', $id, 'is_active', $is_active);
  }





}
