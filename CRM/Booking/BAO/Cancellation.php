<?php

class CRM_Booking_BAO_Cancellation extends CRM_Booking_DAO_Cancellation {


  static function add(&$params){
    $dao = new CRM_Booking_DAO_Cancellation();
    $dao->copyValues($params);
    return $dao->save();
  }
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
  static function create(&$values) {
     $bookingID = CRM_Utils_Array::value('booking_id', $values);
    if(!$bookingID){
      return;
    }else{
      $transaction = new CRM_Core_Transaction();
      try{
        $params = array(
          'option_group_name' => CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS,
          'name' => CRM_Booking_Utils_Constants::OPTION_VALUE_CANCELLED,
        );
        $result = civicrm_api3('OptionValue', 'get', $params);

        $params = array();
        $params['id'] = $bookingID;
        $params['status_id'] =  CRM_Utils_Array::value('value', CRM_Utils_Array::value($result['id'], $result['values']));
        $booking = CRM_Booking_BAO_Booking::create($params);

        $params = array();
        $params['booking_id'] = $bookingID;
        $percentage = CRM_Utils_Array::value('cancellation_percentage', $values);
        $bookingTotal = CRM_Utils_Array::value('booking_total', $values);
        $cancellationFee = (($bookingTotal * $percentage) / 100);

        $additionalCharge = CRM_Utils_Array::value('additional_charge', $values);
        if(is_numeric($additionalCharge)){
          $cancellationFee += $additionalCharge;
          $params['additional_fee'] = $additionalCharge;
        }
        $params['cancellation_date'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('cancellation_date', $values));
        $params['comment'] =  CRM_Utils_Array::value('comment', $values);

        $params['cancellation_fee'] = $cancellationFee;

        self::add($params);

        $slots = CRM_Booking_BAO_Slot::getBookingSlot($bookingID);
        foreach ($slots as $slotId => $slots) {
          $subSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($slotId);
          foreach ($subSlots as $subSlotId => $subSlot) {
          CRM_Booking_BAO_SubSlot::cancel($subSlotId);
          }
         CRM_Booking_BAO_Slot::cancel($slotId);
        }
        // return TRUE;
        
      }catch (Exception $e) {
          $transaction->rollback();
          CRM_Core_Error::fatal($e->getMessage());
      }
    }
  }
}
