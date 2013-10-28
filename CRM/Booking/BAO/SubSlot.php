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


  /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
     * @return object CRM_Booking_DAO_SubSlot object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $dao = new CRM_Booking_DAO_SubSlot();
    $dao->copyValues($params);
    if ($dao->find(TRUE)) {
      CRM_Core_DAO::storeValues($dao, $defaults);
      return $dao;
    }
    return NULL;
  }

  /**
   * Function to delete SubSlot
   *
   * @param  int  $id     Id of the SubSlot to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function del($id) {
    $dao = new CRM_Booking_DAO_SubSlot();
    $dao->id = $id;
    $dao->is_deleted = 1;
    return $dao->save();
  }


  /**
   * Function to delete SubSlot
   *
   * @param  int  $id     Id of the SubSlot to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function cancel($id) {
    $dao = new CRM_Booking_DAO_SubSlot();
    $dao->id = $id;
    $dao->is_cancelled = 1;
    return $dao->save();
  }


   /**
   * Function to compare if an input field is existing in array of slots
   *
   *
   * @param array $fields input parameters to find slot
   * @param array $array array of slots
   *
   * @return boolean, id of matching slot
   *
   * @access public
   * @static
   */
  static function findExistingSubSlot($fields, $slots){
    $keysToUnset = array('id', 'quantity', 'note');
    CRM_Booking_Utils_Array::unsetArray($fields, $keysToUnset);
    foreach ($slots as $key => $value) {
      $id = $value['id'];
      CRM_Booking_Utils_Array::unsetArray($value, $keysToUnset);
      $value['time_required'] = CRM_Utils_Date::processDate($value['time_required']);
      if($fields == $value){
        return array(TRUE, $id);
      }
    }
    return array(FALSE, NULL);
  }




  /**
   * Given the list of params in the params array, fetch the object
   * and store the values in the values array
   *
   * @param array $params input parameters to find object
   * @param array $values output values of the object
   *
   * @return CRM_Event_BAO_ฺSubSlot|null the found object or null
   * @access public
   * @static
   */
  static function getValues(&$params, &$values, &$ids) {
    if (empty($params)) {
      return NULL;
    }
    $subSlot = new CRM_Booking_BAO_SubSlot();
    $subSlot->copyValues($params);
    $subSlot->find();
    $subSlots = array();
    while ($subSlot->fetch()) {
      $ids['subSlot'] = $subSlot->id;
      CRM_Core_DAO::storeValues($subSlot, $values[$subSlot->id]);
      $subSlots[$subSlot->id] = $subSlot;
    }
    return $subSlots;
  }


   static function getSubSlotSlot($slotId){
    $params = array(1 => array( $slotId, 'Integer'));

    $query = "
      SELECT civicrm_booking_sub_slot.id,
             civicrm_booking_sub_slot.slot_id,
             civicrm_booking_sub_slot.resource_id,
             civicrm_booking_sub_slot.config_id,
             civicrm_booking_sub_slot.quantity,
             civicrm_booking_sub_slot.time_required,
             civicrm_booking_sub_slot.note
      FROM civicrm_booking_sub_slot
      WHERE 1
      AND civicrm_booking_sub_slot.slot_id = %1
      AND civicrm_booking_sub_slot.is_deleted = 0;
     ";

    $slots = array();
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $slots[$dao->id] = array(
        'id' => $dao->id,
        'slot_id' => $dao->slot_id,
        'resource_id' => $dao->resource_id,
        'config_id' => $dao->config_id,
        'quantity' => $dao->quantity,
        'time_required' => $dao->time_required,
        'note' => $dao->note,
      );
    }
    return $slots;
  }


}
