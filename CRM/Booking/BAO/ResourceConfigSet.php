<?php

class CRM_Booking_BAO_ResourceConfigSet extends CRM_Booking_DAO_ResourceConfigSet {


    /**
   * takes an associative array and creates a resource config set object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Booking_BAO_ResourceConfigSet object
   * @access public
   * @static
   */
  static function create(&$params) {
    $configSet = new CRM_Booking_DAO_ResourceConfigSet();
    $configSet->copyValues($params);
    return $configSet->save();
  }

    /**
   * Function to delete Resource
   *
   * @param  int  $id     Id of the Resource Config to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function del($id) {
    $configSet = new CRM_Booking_DAO_ResourceConfigSet();
    $configSet->id = $id;
    $configSet->is_deleted = 1;
    return $configSet->save();
  }


  /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
     * @return object CRM_Booking_DAO_Resource object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $configSet = new CRM_Booking_DAO_ResourceConfigSet();
    $configSet->copyValues($params);
    if ($configSet->find(TRUE)) {
      CRM_Core_DAO::storeValues($configSet, $defaults);
      return $configSet;
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
    return CRM_Core_DAO::setFieldValue('CRM_Booking_DAO_ResourceConfigSet', $id, 'is_active', $is_active);
  }






}
