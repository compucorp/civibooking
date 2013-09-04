<?php

class CRM_Booking_BAO_Booking extends CRM_Booking_DAO_Booking {


  static function add(&$params){
    $bookingDAO = new CRM_Booking_DAO_Booking();
    $bookingDAO->copyValues($params);
    return $bookingDAO->save();
  }


    /**
   * takes an associative array and creates a booking object
   *
   * the function extract all the params it needs to initialize the create a
   * booking object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   *
   * @return object CRM_Booking_BAO_Booking object
   * @access public
   * @static
   */
  static function create(&$params) {

    $resources = $params['resources'];

    unset($params['resources']);
    unset($params['version']);

    $transaction = new CRM_Core_Transaction();
    try{
      $booking = self::add($params);
      $bookingID = $booking->id;

      foreach ($resources as $key => $resource) {
        $slot = array(
          'version' => 3,
          'booking_id' => $bookingID,
          'config_id' => CRM_Utils_Array::value('configuration_id', $resource),
          'start' => CRM_Utils_Array::value('start_date', $resource),
          'end' => CRM_Utils_Array::value('end_date', $resource),
          'resource_id' =>  CRM_Utils_Array::value('resource_id', $resource),
          //'quantity' => CRM_Utils_Array::value('quantity', $resource), should go to price set
          //'note' => CRM_Utils_Array::value('note', $resource), //TODO::Passing note from the UK
        );
        $slotResult = civicrm_api('Slot', 'Create', $slot);
          $slotID =  CRM_Utils_Array::value('id', $slotResult);
          $subResources = $resource['sub_resources'];
          foreach($subResources as $subKey => $subResource){
            $subSlot = array(
              'version' => 3,
              'resource_id' =>  CRM_Utils_Array::value('resource_id', $resource),
              'slot_id' => $slotID,
              'config_id' => CRM_Utils_Array::value('configuration_id', $resource),
              'time_required' => date() //FIXED:: get the time from the UI
              //'note' => CRM_Utils_Array::value('note', $resource), //TODO::Passing note from the UI
            );
            $subSlotResult = civicrm_api('SubSlot', 'Create', $subSlot);
          }
      }
      return $booking;
    }catch (Exception $e) {
      $transaction->rollback();
      CRM_Core_Error::fatal($e->getMessage());
    }

  }


}
