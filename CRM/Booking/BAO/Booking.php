<?php

class CRM_Booking_BAO_Booking extends CRM_Booking_DAO_Booking {

   /**
   * static field for all the booking information that we can potentially export
   *
   * @var array
   * @static
   */
  static $_exportableFields = NULL;


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


  /**
   * Given the list of params in the params array, fetch the object
   * and store the values in the values array
   *
   * @param array $params input parameters to find object
   * @param array $values output values of the object
   *
   * @return CRM_Event_BAO_ฺBooking|null the found object or null
   * @access public
   * @static
   */
  static function getValues(&$params, &$values, &$ids) {
    if (empty($params)) {
      return NULL;
    }
    $booking = new CRM_Booking_DAO_Booking();
    $booking->copyValues($params);
    $booking->find();
    $bookings = array();
    while ($booking->fetch()) {
      $ids['booking'] = $booking->id;
      CRM_Core_DAO::storeValues($booking, $values[$booking->id]);
      $bookings[$booking->id] = $booking;
    }
    return $bookings;
  }

  static function getBookingContactCount($contactId){
    $params = array(1 => array( $contactId, 'Integer'));
    $query = "SELECT COUNT(DISTINCT(id)) AS count  FROM civicrm_booking WHERE primary_contact_id = %1";
    return CRM_Core_DAO::singleValueQuery($query, $params);
  }


  /**
   * Get the exportable fields for Booking
   *
   *
   * @return array array of exportable Fields
   * @access public
   * @static
   */
  static function &exportableFields() {
    if (!isset(self::$_exportableFields["booking"])) {
      self::$_exportableFields["booking"] = array();

      $exportableFields = CRM_Booking_DAO_Booking::export();

      $bookingFields = array(
        'booking_title' => array('title' => ts('Title'), 'type' => CRM_Utils_Type::T_STRING),
        'booking_po_no' => array('title' => ts('PO Number'), 'type' => CRM_Utils_Type::T_STRING),
        'booking_status' => array('title' => ts('Booking Status'), 'type' => CRM_Utils_Type::T_STRING),
        'booking_payment_status' => array('title' => ts('Booking Status'), 'type' => CRM_Utils_Type::T_STRING),
      );

      $fields = array_merge($bookingFields, $exportableFields);

      self::$_exportableFields["booking"] = $fields;
    }
    return self::$_exportableFields["booking"];
  }


}
