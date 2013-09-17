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


   static function getSubSlotSlot($slotId){
    $params = array(1 => array( $slotId, 'Integer'));

    $query = "
      SELECT civicrm_booking_sub_slot.id,
             civicrm_booking_sub_slot.slot_id,
             civicrm_booking_sub_slot.resource_id,
             civicrm_booking_sub_slot.config_id,
             civicrm_booking_sub_slot.time_required,
             civicrm_booking_sub_slot.note
      FROM civicrm_booking_sub_slot
      INNER JOIN civicrm_booking_slot ON civicrm_booking_slot.id = civicrm_booking_sub_slot.slot_id
      WHERE 1
      AND civicrm_booking_slot.id = %1
     ";

    $slots = array();
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $slots[$dao->id] = array(
        'id' => $dao->id,
        'slot_id' => $dao->slot_id,
        'resource_id' => $dao->resource_id,
        'config_id' => $dao->config_id,
        'time_required' => $dao->time_required,
        'note' => $dao->note,
      );
    }
    return $slots;
  }


}
