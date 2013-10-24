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
    $isUpdate = FALSE;
    $resources = $params['resources'];
    $adhocCharges = $params['adhoc_charges'];

    dpr($adhocCharges);

    if($params['validate']){
      //TODO:: Validate resource
      //$result = array();
      //$isValid = CRM_Booking_BAO_Slot::validate($resources, $result);
      //if(!$result['isValid']){
        //return list of object that invalid
        ///return error message
      //}
    }
    unset($params['resources']);
    unset($params['version']);
    unset($params['adhoc_charges']);
    unset($params['validate']);
    $transaction = new CRM_Core_Transaction();
    $lineItem = array(
        'version' => 3,
        'sequential' => 1,
    );
    try{
      $booking = self::add($params);
      $bookingID = $booking->id;

      if(isset($params['id'])){ //booking id was passed from the form so we are on edit mode
        $isUpdate = TRUE;
        $currentSlots = CRM_Booking_BAO_Slot::getBookingSlot($bookingID);
      }
      $newSlotIds = array();
      $newSubSlotIds = array();
      foreach ($resources as $key => $resource) {
        $slot = array(
          'booking_id' => $bookingID,
          'config_id' => CRM_Utils_Array::value('configuration_id', $resource),
          'start' => CRM_Utils_Date::processDate(CRM_Utils_Array::value('start_date', $resource)),
          'end' => CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $resource)),
          'resource_id' =>  CRM_Utils_Array::value('resource_id', $resource),
          'quantity' => CRM_Utils_Array::value('quantity', $resource),
          'note' => CRM_Utils_Array::value('note', $resource),
        );
        if($isUpdate){
          $fields = array(
            'resource_id' =>  CRM_Utils_Array::value('resource_id', $resource),
            'config_id' =>  CRM_Utils_Array::value('configuration_id', $resource),
            'start' =>  CRM_Utils_Date::processDate(CRM_Utils_Array::value('start_date', $resource)),
            'end' => CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $resource)),
          );
          list($isExist, $currentID) = CRM_Booking_BAO_Slot::findExistingSlot($fields, $currentSlots);
          if($isExist){
            $slot['id'] = $currentID;
          }
        }
        $slotResult = civicrm_api3('Slot', 'create', $slot);
        $slotID =  CRM_Utils_Array::value('id', $slotResult);
        array_push($newSlotIds, $slotID);

        if($isUpdate){
          $currentSubSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($slotID);
        }
        $subResources = $resource['sub_resources'];
        foreach($subResources as $subKey => $subResource){
          $subSlot = array(
            'resource_id' =>  CRM_Utils_Array::value('resource_id', $subResource),
            'slot_id' => $slotID,
            'config_id' => CRM_Utils_Array::value('configuration_id', $subResource),
            'time_required' =>  CRM_Utils_Date::processDate(CRM_Utils_Array::value('time_required', $subResource)),
            'quantity' => CRM_Utils_Array::value('quantity', $subResource),
            'note' => CRM_Utils_Array::value('note', $subResource),
          );
          if($isUpdate){
            $fields = array(
              'resource_id' =>  CRM_Utils_Array::value('resource_id', $subResource),
              'slot_id' => $slotID,
              'config_id' => CRM_Utils_Array::value('configuration_id', $subResource),
              'time_required' =>  CRM_Utils_Date::processDate(CRM_Utils_Array::value('time_required', $subResource)),
            );

            list($isExist, $currentSubSlotId) =  CRM_Booking_BAO_SubSlot::findExistingSubSlot($fields, $currentSubSlots);
            if($isExist){
              $subSlot['id'] = $currentSubSlotId;
            }
          }
          $subSlotResult = civicrm_api3('SubSlot', 'Create', $subSlot);
          $subSlotID =  CRM_Utils_Array::value('id', $subSlotResult);
          array_push($newSubSlotIds, $subSlotID);
        }
        if($isUpdate){ //remove subslots that have been removed
          $subSlotsToBeRemoved = array();
          foreach ($currentSubSlots as $key => $currentSubSlot) {
            if(!in_array($key, $newSubSlotIds)){
              $subSlotsToBeRemoved[$key] = $currentSubSlot;
            }
          }
          if(!empty($subSlotsToBeRemoved)){
            foreach ($subSlotsToBeRemoved as $key => $slot) {
              civicrm_api3('SubSlot', 'delete', array('id' => $key));
            }
          }
        }
      }

      if($isUpdate){ //remove all slots that have been removed
        $slotsToBeRemoved = array();
        foreach ($currentSlots as $key => $currentSlot) {
          if(!in_array($key, $newSlotIds)){
            $slotsToBeRemoved[$key] = $currentSlot;
          }
        }
        if(!empty($slotsToBeRemoved)){
          foreach ($slotsToBeRemoved as $key => $slot) {
            civicrm_api3('Slot', 'delete', array('id' => $key));
          }
        }
      }

      if($adhocCharges){
        if($isUpdate){
          $result = civicrm_api3('AdhocCharges', 'get', array('booking_id' => $bookingID));
          $currentAdhocCharges = $result['values'];
        }
        $items = CRM_Utils_Array::value('items', $adhocCharges);
        dpr($items);
        foreach ($items as $key => $item) {
          //dpr($item);
          $params = array(
            'booking_id' =>  $bookingID,
            'item_id' => CRM_Utils_Array::value('id', $item),
            'quantity' => CRM_Utils_Array::value('quantity', $item),
          );
          if($isUpdate){
            $fields = array(
              'booking_id' =>  $bookingID,
              'item_id' => CRM_Utils_Array::value('id', $item),
            );
            list($isExist, $currentAdhocChargesId) =  CRM_Booking_BAO_AdhocCharges::findExistingAdhocCharges($fields, $currentAdhocCharges);
            if($isExist){
              $params['id'] =  $currentAdhocChargesId;
            }
          }
          civicrm_api3('AdhocCharges', 'create', $params);
        }
      }
      return $booking;
    }
    catch (CiviCRM_API3_Exception $e) {
      $transaction->rollback();
      CRM_Core_Error::fatal($e->getMessage());
    }
    catch (Exception $e) {
      $transaction->rollback();
      CRM_Core_Error::fatal($e->getMessage());
    }

  }


  static function recordContribution($values){
    $bookingID = CRM_Utils_Array::value('booking_id', $values);
    if(!CRM_Utils_Array::value('booking_id', $values)){
      return;
    }else{
      try{
       $transaction = new CRM_Core_Transaction();
       $params = array(
          'version' => 3,
          'sequential' => 1,
          'contact_id' => CRM_Utils_Array::value('payment_contact', $values),
          'financial_type_id' => CRM_Utils_Array::value('financial_type_id', $values),
          'total_amount' =>  CRM_Utils_Array::value('total_amount', $values),
          'payment_instrument_id' =>  CRM_Utils_Array::value('payment_instrument_id', $values),
          'receive_date' =>  CRM_Utils_Array::value('receive_date', $values),
          'contribution_status_id' =>  CRM_Utils_Array::value('contribution_status_id', $values),
          'source' => CRM_Utils_Array::value('booking_title', $values),
          'trxn_id' =>  CRM_Utils_Array::value('trxn_id', $values),
        );
        $contribution = civicrm_api('Contribution', 'create', $params);
        $contributionId = CRM_Utils_Array::value('id', $contribution);
        if($contributionId){
          $payment = array('booking_id' => $bookingID, 'contribution_id' => $contributionId);
          CRM_Booking_BAO_Payment::create($payment);
        }

        $result = civicrm_api('Slot', 'get', array('version' => 3, 'booking_id' => $bookingID));
        $slots = CRM_Utils_Array::value('values', $result);
        $lineItem = array('version' => 3, 'financial_type_id' => CRM_Utils_Array::value('financial_type_id', $values));
        foreach ($slots as $slot) {
          $slotID = $slot['id'];
          $configId =  CRM_Utils_Array::value('config_id', $slot);
          $configResult = civicrm_api('ResourceConfigOption', 'get', array('version' => 3, 'id' => $configId));
          $config = CRM_Utils_Array::value('values', $configResult);
          $unitPrice = CRM_Utils_Array::value('price', $config[$configId]);
          $qty = CRM_Utils_Array::value('quantity', $slot);

          $lineItem['label'] = CRM_Utils_Array::value('label', $config[$configId]);
          $lineItem['entity_table'] = "civicrm_booking_slot";
          $lineItem['entity_id'] = $slotID;
          $lineItem['qty'] = $qty;
          $lineItem['unit_price'] = $unitPrice;
          $lineItem['line_total'] =   self::_calLineTotal($unitPrice, $qty);
          $lineItemResult = civicrm_api('LineItem', 'create', $lineItem);
          $result = civicrm_api('SubSlot', 'get', array('version' => 3 ,'slot_id' => $slotID));
          $subSlots = CRM_Utils_Array::value('values', $result);
          foreach ($subSlots as $subSlot) {
            $subSlotID = $subSlot['id'];
            $configId =  CRM_Utils_Array::value('config_id', $subSlot);
            $configResult = civicrm_api('ResourceConfigOption', 'get', array('version' => 3, 'id' => $configId));
            $config = CRM_Utils_Array::value('values', $configResult);
            $unitPrice = CRM_Utils_Array::value('price', $config[$configId]);
            $qty = CRM_Utils_Array::value('quantity', $subSlot);

            $lineItem['label'] = CRM_Utils_Array::value('label', $config[$configId]);
            $lineItem['entity_table'] = "civicrm_booking_sub_slot";
            $lineItem['entity_id'] = $subSlotID;
            $lineItem['qty'] = $qty;
            $lineItem['unit_price'] = $unitPrice;
            $lineItem['line_total'] =  self::_calLineTotal($unitPrice, $qty);
            $lineItemResult = civicrm_api('LineItem', 'create', $lineItem);
          }
        }

        $adhocChargesResult = civicrm_api('AdhocCharges', 'get', array('version' => 3, 'booking_id' => $bookingID));
        $adhocChargesValues = CRM_Utils_Array::value('values', $adhocChargesResult);
        foreach ($adhocChargesValues as $id => $adhocCharges) {

          $itemId =  CRM_Utils_Array::value('item_id', $adhocCharges);
          $itemResult = civicrm_api('AdhocChargesItem', 'get', array('version' => 3, 'id' => $itemId));
          $itemValue = CRM_Utils_Array::value('values', $itemResult);
          $unitPrice = CRM_Utils_Array::value('price', $itemValue[$itemId]);
          $qty = CRM_Utils_Array::value('quantity', $adhocCharges);

          $lineItem['entity_table'] = "civicrm_booking_adhoc_charges";
          $lineItem['entity_id'] = $id;
          $lineItem['unit_price'] = $unitPrice;
          $lineItem['qty'] = $qty;
          $lineItem['label'] = CRM_Utils_Array::value('label', $itemValue[$itemId]);
          $lineItem['line_total'] = self::_calLineTotal($unitPrice, $qty);
          $lineItemResult = civicrm_api('LineItem', 'create', $lineItem);
        }

        //TODO:: create financial item or not?
      }catch (Exception $e) {
          $transaction->rollback();
          CRM_Core_Error::fatal($e->getMessage());
      }
    }

  }

  static function _calLineTotal($unitPrice, $qty){
    return $unitPrice * $qty;
  }



    /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
     * @return object CRM_Booking_DAO_Booking object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $dao = new CRM_Booking_DAO_Booking();
    $dao->copyValues($params);
    if ($dao->find(TRUE)) {
      CRM_Core_DAO::storeValues($dao, $defaults);
      return $dao;
    }
    return NULL;
  }


  static function getBookingDetails($id){
    $slots = CRM_Booking_BAO_Slot::getBookingSlot($id);
    $subSlots = array();
    foreach ($slots as $key => $slot) {
      $label =  CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_Resource',
        $slot['resource_id'],
        'label',
        'id'
      );
      //Quite expensive
      $slots[$key]['resource_label'] = $label;
      $slots[$key]['config_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
        $slot['config_id'],
        'label',
        'id'
      );
      $params = array(
          'version' => 3,
          'entity_id' => $slot['id'],
          'entity_table' => 'civicrm_booking_slot',
        );
      $result = civicrm_api('LineItem', 'get', $params);
      if(!empty($result['values'])){
        $lineItem = CRM_Utils_Array::value($result['id'], $result['values']);
        $slots[$key]['unit_price'] = CRM_Utils_Array::value('unit_price', $lineItem);
        $slots[$key]['total_amount'] = CRM_Utils_Array::value('line_total', $lineItem);
        $slots[$key]['quantity'] = CRM_Utils_Array::value('qty', $lineItem);
      }else{ //calulate manuanlly
        $slots[$key]['total_amount'] = CRM_Booking_BAO_Booking::calulateSlotPrice($slot['config_id'], $slot['quantity']);
        $slots[$key]['unit_price'] = CRM_Core_DAO::getFieldValue(
            'CRM_Booking_DAO_ResourceConfigOption',
            $slot['config_id'],
            'price',
            'id'
        );
      }
      $childSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($key);
      foreach ($childSlots as $key => $subSlot) {
        $subSlot['resource_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_Resource',
          $subSlot['resource_id'],
          'label',
          'id'
        );
        $subSlot['config_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
          $subSlot['config_id'],
          'label',
          'id'
        );
        $params = array(
          'version' => 3,
          'entity_id' => $subSlot['id'],
          'entity_table' => 'civicrm_booking_sub_slot',
        );
        $result = civicrm_api('LineItem', 'get', $params);
        if(!empty($result['values'])){
          $subSlotlineItem = CRM_Utils_Array::value($result['id'], $result['values']);
          $subSlot['unit_price'] = CRM_Utils_Array::value('unit_price', $subSlotlineItem);
          $subSlot['total_amount'] = CRM_Utils_Array::value('line_total', $subSlotlineItem);
          $subSlot['quantity'] = CRM_Utils_Array::value('qty', $subSlotlineItem);
        }else{ //calulate manuanlly
          $subSlot['total_amount'] = CRM_Booking_BAO_Booking::calulateSlotPrice($subSlot['config_id'], $subSlot['quantity']);
          $subSlot['unit_price'] = CRM_Core_DAO::getFieldValue(
            'CRM_Booking_DAO_ResourceConfigOption',
            $subSlot['config_id'],
            'price',
            'id'
          );
        }

        $subSlot['parent_resource_label'] =  $label;
        $subSlots[$subSlot['id']] = $subSlot;
      }
    }
    //get adhoc charges
    $adhocCharges = array();
    $adhocChargesResult = civicrm_api3('AdhocCharges', 'get', array('booking_id' => $id));
    $adhocChargesValues = CRM_Utils_Array::value('values', $adhocChargesResult);
    foreach ($adhocChargesValues as $id => $charges) {
        $charges['item_label'] = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_AdhocChargesItem',
          $charges['item_id'],
          'label',
          'id'
        );
        $params = array(
          'entity_id' => $charges['id'],
          'entity_table' => 'civicrm_booking_adhoc_charges',
        );
        $result = civicrm_api3('LineItem', 'get', $params);	//get LineItem record wheather the booking has contribution or not.
        if(!empty($result['values'])){
          $chargesLineItem = CRM_Utils_Array::value($result['id'], $result['values']);
          $charges['unit_price'] = CRM_Utils_Array::value('unit_price', $chargesLineItem);
          $charges['total_amount'] = CRM_Utils_Array::value('line_total', $chargesLineItem);
          $charges['quantity'] = CRM_Utils_Array::value('qty', $chargesLineItem);
        }else{ //calulate manuanlly
          //this error
          /*$charges['total_amount'] = CRM_Booking_BAO_Booking::calulateSlotPrice($charges['config_id'], $charges['quantity']);
          $charges['unit_price'] = CRM_Core_DAO::getFieldValue(
            'CRM_Booking_DAO_AdhocChargesItem',
            $charges['item_id'],
            'price',
            'id'
          );*/
        }
        $adhocCharges[$id] = $charges;
    }
    //get cancellation charges
    $cancellationCharges = array();
  	$cancellationsResult = civicrm_api3('Cancellation','get',array('booking_id' => $id));
	  $cancellationsValues = CRM_Utils_Array::value('values',$cancellationsResult);
	  foreach ($cancellationsValues as $key => $cancels) {
		  //get LineItem record wheather the booking has contribution or not.
		  $params = array(
        'entity_id' => $cancels['id'],
        'entity_table' => 'civicrm_booking_cancellation',
      );
      $lineItemResult = civicrm_api3('LineItem', 'get', $params);	//retrieve LineItem record.
      if(!empty($lineItemResult['values'])){
        $cancelsLineItem = CRM_Utils_Array::value($lineItemResult['id'], $lineItemResult['values']);
	    	//TODO: define cancellation fee from LineItem table
        //$charges['unit_price'] = CRM_Utils_Array::value('unit_price', $chargesLineItem);
      }else{ //otherwise calulate manuanlly
        $cancels['total_fee'] = $cancels['cancellation_fee'] + $cancels['additional_fee'];
      }
		  //get booking price
		  $params = array('booking_id' => $cancels['booking_id']);
		  $bookingItem = civicrm_api3('Booking','get',$params);
		  foreach (CRM_Utils_Array::value('values',$bookingItem) as $k => $v) {
			  $cancels['booking_price'] = CRM_Utils_Array::value('total_amount',$v) - CRM_Utils_Array::value('discount_amount',$v);
        $cancels['event_date'] = CRM_Utils_Array::value('event_date',$v);
		  }

      //calculate the total amount of cancellation charge
      $cancels['cancellation_total_fee'] = $cancels['cancellation_fee'] + $cancels['additional_fee'];

      //calculate how many days before event date
      $cancellation_date = new DateTime($cancels['cancellation_date']);
      $eventDate = new DateTime($cancels['event_date']);
      $interval = $cancellation_date->diff($eventDate);
      $cancels['prior_days'] = $interval->days;

      $cancellationCharges[$key] = $cancels;
	  }
	  //get contribution
    $contribution = array();
    $bookingPaymentResult = civicrm_api3('BookingPayment','get',array('booking_id' => $id));
    $bookingPaymentValues = CRM_Utils_Array::value('values',$bookingPaymentResult); //get contribution id from booking_payment
    foreach ($bookingPaymentValues as $key => $bpValues) {
        $contributionResult = civicrm_api3('Contribution','get',array('id' => $bpValues['contribution_id']));   //get contribution record
        $contributionValues = CRM_Utils_Array::value('values',$contributionResult);
        foreach ($contributionValues as $k => $conValues) {
            $contribution[$k] = $conValues;
        }
    }

    return array(
      'slots' => $slots,
      'sub_slots' => $subSlots,
      'adhoc_charges' => $adhocCharges,
      'cancellation_charges' =>$cancellationCharges,
      'contribution' => $contribution);
  }

  /**
   * Function to delete Booking
   *
   * @param  int  $id     Id of the Resoruce to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function del($id) {
    $transaction = new CRM_Core_Transaction();
    try{
      $slots = CRM_Booking_BAO_Slot::getBookingSlot($id);
        foreach ($slots as $slotId => $slots) {
          $subSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($slotId);
          foreach ($subSlots as $subSlotId => $subSlot) {
          CRM_Booking_BAO_SubSlot::del($subSlotId);
        }
        CRM_Booking_BAO_Slot::del($slotId);
      }
      $dao = new CRM_Booking_DAO_Booking();
      $dao->id = $id;
      $dao->is_deleted = 1;
      return $dao->save();

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
   * @return CRM_Event_BAO_���Booking|null the found object or null
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
    $query = "SELECT COUNT(DISTINCT(id)) AS count
              FROM civicrm_booking
              WHERE 1
              AND (primary_contact_id = %1 OR secondary_contact_id = %1)
              AND is_deleted = 0 ";
    return CRM_Core_DAO::singleValueQuery($query, $params);
  }

  static function getContactAssociatedBooking($contactId){

    $params = array(1 => array( $contactId, 'Integer'));

    $query = "SELECT civicrm_booking.id as id,
                     civicrm_booking.primary_contact_id,
                     civicrm_contact.display_name as parimary_contact_name,
                     civicrm_booking.title as title,
                     civicrm_booking.created_date as created_date,
                     civicrm_booking.event_date as event_date,
                     civicrm_booking.total_amount as total_amount,
                     payment_status_value.label as payment_status,
                     booking_status_value.label as booking_status
              FROM civicrm_booking
              INNER JOIN civicrm_contact ON civicrm_contact.id = civicrm_booking.primary_contact_id
              INNER JOIN civicrm_option_group booking_status_group ON booking_status_group.name = 'booking_status'
              INNER JOIN civicrm_option_value booking_status_value ON booking_status_value.value = civicrm_booking.status_id
                                             AND booking_status_group.id = booking_status_value.option_group_id
              LEFT JOIN civicrm_booking_payment ON civicrm_booking_payment.booking_id = civicrm_booking.id
              LEFT JOIN civicrm_contribution ON civicrm_contribution.id = civicrm_booking_payment.contribution_id
              LEFT JOIN civicrm_option_group payment_status_group ON payment_status_group.name = 'contribution_status'
              LEFT JOIN civicrm_option_value payment_status_value ON payment_status_value.value = civicrm_contribution.contribution_status_id
                                             AND payment_status_group.id = payment_status_value.option_group_id
              WHERE civicrm_booking.secondary_contact_id = %1
              AND civicrm_booking.is_deleted = 0";

    $bookings = array();
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $bookings[$dao->id] = array(
        'id' => $dao->id,
        'primary_contact_id' => $dao->primary_contact_id,
        'primary_contact_name' => $dao->parimary_contact_name,
        'title' => $dao->title,
        'created_date' => $dao->created_date,
        'event_date' => $dao->event_date,
        'total_amount' => $dao->total_amount,
        'booking_payment_status' => $dao->payment_status,
        'booking_status' => $dao->booking_status
      );
    }
    return $bookings;
  }

  static function getPaymentStatus($id){
    $params = array(1 => array( $id, 'Integer'));
    $query = "SELECT civicrm_option_value.label as status
              FROM civicrm_booking
              LEFT JOIN civicrm_booking_payment ON civicrm_booking_payment.booking_id = civicrm_booking.id
              LEFT JOIN civicrm_contribution ON civicrm_contribution.id = civicrm_booking_payment.contribution_id
              LEFT JOIN civicrm_option_group ON civicrm_option_group.name = 'contribution_status'
              LEFT JOIN civicrm_option_value ON civicrm_option_value.value = civicrm_contribution.contribution_status_id
                                             AND civicrm_option_group.id = civicrm_option_value.option_group_id
              WHERE civicrm_booking.id = %1";
    return CRM_Core_DAO::singleValueQuery($query, $params);

  }


   /**
   * Get the values for pseudoconstants for name->value and reverse.
   *
   * @param array   $defaults (reference) the default values, some of which need to be resolved.
   * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
   *
   * @return void
   * @access public
   * @static
   */
  static function resolveDefaults(&$defaults, $reverse = FALSE) {
    $statusIds =  CRM_Booking_BAO_Booking::buildOptions('status_id', 'create');
    self::lookupValue($defaults, 'status', $statusIds, $reverse);
  }

  /**
   * This function is used to convert associative array names to values
   * and vice-versa.
   *
   * This function is used by both the web form layer and the api. Note that
   * the api needs the name => value conversion, also the view layer typically
   * requires value => name conversion
   */
  static function lookupValue(&$defaults, $property, &$lookup, $reverse) {
    $id = $property . '_id';

    $src = $reverse ? $property : $id;
    $dst = $reverse ? $id : $property;

    if (!array_key_exists($src, $defaults)) {
      return FALSE;
    }

    $look = $reverse ? array_flip($lookup) : $lookup;

    if (is_array($look)) {
      if (!array_key_exists($defaults[$src], $look)) {
        return FALSE;
      }
    }
    $defaults[$dst] = $look[$defaults[$src]];
    return TRUE;
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


  static function getBookingAmount($id){
    if(!$id){
      return;
    }
    $bookingAmount = array(
      'resource_fees' => 0,
      'sub_resource_fees' => 0,
      'adhoc_charges_fees' => 0,
      'discount_amount' => 0,
      'total_amount' => 0,
    );
    self::retrieve($params = array('id' => $id), $booking);
    $bookingAmount['discount_amount'] = CRM_Utils_Array::value('discount_amount', $booking);
    $bookingAmount['total_amount'] = CRM_Utils_Array::value('total_amount', $booking);
    $slots = CRM_Booking_BAO_Slot::getBookingSlot($id);
    $subSlots = array();
    foreach ($slots as $key => $slot) {
      $subSlotResult = CRM_Booking_BAO_SubSlot::getSubSlotSlot($slot['id']);
      foreach ($subSlotResult as $key => $subSlot) {
        $subSlots[$key] = $subSlot;
      }
    }
    $adhocCharges = CRM_Booking_BAO_AdhocCharges::getBookingAdhocCharges($id);
    CRM_Booking_BAO_Payment::retrieve($params = array('booking_id' => $id), $payment);
    if(!empty($payment) && isset($payment['contribution_id'])){ // contribution exit so get all price from line item
      /*
      $params = array(
        'version' => 3,
        'id' => $payment['contribution_id'],
        );
      $result = civicrm_api('Contribution', 'get', $params);
      $contribution = CRM_Utils_Array::value($payment['contribution_id'], $result['values'] );
      $bookingAmount['total_amount']  = CRM_Utils_Array::value('total_amount', $contribution);
      */
      foreach ($slots as $slotId => $slot) {
        $params = array(
          'version' => 3,
          'entity_id' => $slotId,
          'entity_table' => 'civicrm_booking_slot',
        );
        $result = civicrm_api('LineItem', 'get', $params);
        $lineItem = CRM_Utils_Array::value($result['id'], $result['values']);
        $bookingAmount['resource_fees']  += CRM_Utils_Array::value('line_total', $lineItem);
      }
      foreach ($subSlots as $subSlotId => $subSlots) {
        $params = array(
          'version' => 3,
          'entity_id' => $subSlotId,
          'entity_table' => 'civicrm_booking_sub_slot',
        );
        $result = civicrm_api('LineItem', 'get', $params);
        $lineItem = CRM_Utils_Array::value($result['id'], $result['values']);
        $bookingAmount['sub_resource_fees']  += CRM_Utils_Array::value('line_total', $lineItem);
      }
      foreach ($adhocCharges as $charges) {
        $params = array(
          'version' => 3,
          'entity_id' => CRM_Utils_Array::value('id', $charges),
          'entity_table' => 'civicrm_booking_adhoc_charges',
        );
        $result = civicrm_api('LineItem', 'get', $params);
        $lineItem = CRM_Utils_Array::value($result['id'], $result['values']);
        $bookingAmount['adhoc_charges_fees']  += CRM_Utils_Array::value('line_total', $lineItem);
      }
    }else{
      foreach ($slots as $id => $slot) {
        $bookingAmount['resource_fees'] += self::calulateSlotPrice(CRM_Utils_Array::value('config_id', $slot) ,CRM_Utils_Array::value('quantity', $slot));
      }
      foreach ($subSlots as $id => $subSlot) {
        $bookingAmount['sub_resource_fees'] += self::calulateSlotPrice(CRM_Utils_Array::value('config_id', $subSlot) ,CRM_Utils_Array::value('quantity', $subSlot));
      }
      foreach ($adhocCharges as $charges) {
        $price = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_AdhocChargesItem',
          CRM_Utils_Array::value('item_id', $charges) ,
          'price',
          'id'
        );
        $bookingAmount['adhoc_charges_fees'] += ($price * CRM_Utils_Array::value('quantity', $charges));
      }
    }
    return $bookingAmount;
  }

  static function calulateSlotPrice($configId, $qty){
    if(!$configId & !$qty){
      return NULL;
    }
    $price = CRM_Core_DAO::getFieldValue('CRM_Booking_DAO_ResourceConfigOption',
      $configId,
      'price',
      'id'
    );
    return $price * $qty;
  }

  static function createActivity($params){
    $session =& CRM_Core_Session::singleton( );
    $userId = $session->get( 'userID' ); // which is contact id of the user
    $optionValue = civicrm_api3('OptionValue', 'get',
      array(
       'option_group_name' => 'activity_type',
       'name' => CRM_Booking_Utils_Constants::ACTIVITY_TYPE
      )
    );
    $activityTypeId = $optionValue['values'][$optionValue['id']]['value'];
    $params = array(
      'source_contact_id' => $userId,
      'activity_type_id' => $activityTypeId,
      'subject' =>  CRM_Utils_Array::value('subject', $params),
      'activity_date_time' => date('YmdHis'),
      'target_contact_id' => CRM_Utils_Array::value('target_contact_id', $params),
      'status_id' => 2,
      'priority_id' => 1,
    );
    $result = civicrm_api3('Activity', 'create', $params);
  }


  /**
   * Process that send e-mails
   *
   * @return void
   * @access public
   */
  static function sendMail($contactID, &$values, $isTest = FALSE, $returnMessageText = FALSE) {
    //TODO:: check if from email address is entered
    $config = CRM_Booking_BAO_BookingConfig::getConfig();

    $template = CRM_Core_Smarty::singleton( );

    list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactID);

    //send email only when email is present
    if ($email) {

    	//get booking detail
    	$bookingDetail = CRM_Booking_BAO_Booking::getBookingDetails($values['booking_id']);
    	$slots = CRM_Utils_Array::value('slots', $bookingDetail);
    	$subSlots = CRM_Utils_Array::value('sub_slots', $bookingDetail);
    	$adhocCharges = CRM_Utils_Array::value('adhoc_charges', $bookingDetail);
    	$cancellationCharges = CRM_Utils_Array::value('cancellation_charges', $bookingDetail);

      $tplParams = array(
        'email' => $email,
        'today_date' => date('d.m.Y'),
        'booking_id' => $values['booking_id'],
        'booking_title' => $values['booking_title'],
        'booking_status' => $values['booking_status'],
        'booking_event_date' => $values['event_date'],
        'booking_event_day' => date('l',$values['event_date']),
        'participants_estimate' => $values['participants_estimate'],
        'participants_actual' => $values['participants_actual'],
      	'slots' => $slots,
      	'sub_slots' => $subSlots,
      	'adhoc_charges' => $adhocCharges,
      	'cancellation_charges' => $cancellationCharges,

        //TODO:: build the booking tpl
      );
      $sendTemplateParams = array(
        'groupName' => 'msg_tpl_workflow_booking',
        'valueName' => 'booking_offline_receipt',
        'contactId' => $contactID,
        'isTest' => $isTest,
        'tplParams' => $tplParams,
        'PDFFilename' => 'bookingReceipt.pdf',
      );

      if(CRM_Utils_Array::value('include_payment_info', $values)){
        //TODO: add contribution detail
        $sendTemplateParams['tplParams']['contribution'] = NULL;
      }

      // address required during receipt processing (pdf and email receipt)
      //TODO:: add addresss
      if ($displayAddress = CRM_Utils_Array::value('address', $values)) {
        $sendTemplateParams['tplParams']['address'] = $displayAddress;
      }

      //TODO:: add line item tpl params
      if ($lineItem = CRM_Utils_Array::value('lineItem', $values)) {
        $sendTemplateParams['tplParams']['lineItem'] = $lineItem;
      }

      $sendTemplateParams['from'] =  $values['from_email_address'];
      $sendTemplateParams['toName'] = $displayName;
      $sendTemplateParams['toEmail'] = $email;
      //$sendTemplateParams['autoSubmitted'] = TRUE;
      $cc = CRM_Utils_Array::value('cc_email_address', $config);
      if($cc){
        $sendTemplateParams['cc'] = $cc;
      }
      $bcc = CRM_Utils_Array::value('bcc_email_address', $config);
      if($bcc){
        $sendTemplateParams['bcc'] = $bcc;
      }

      list($sent, $subject, $message, $html)  = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);

      if($sent & CRM_Utils_Array::value('log_confirmation_email', $config)){
          $params = array(
            'version' => 3,
            'option_group_name' => 'activity_type',
            'name' => 'Email',
          );
          $optionValue = civicrm_api('OptionValue', 'get', $params);
          $activityTypeId = $optionValue['values'][$optionValue['id']]['value'];
          $params = array(
            'version' => 3,
            /*'source_contact_id' => $values['source_contact_id'],*/
            'activity_type_id' => $activityTypeId,
            'subject' => ts('Booking Confirmation Email'),
            'activity_date_time' => date('YmdHis'),
            'target_contact_id' => $contactID,
            'status_id' => 2,
            'priority_id' => 1,
          );
          $result = civicrm_api('Activity', 'create', $params);
       }
      if ($returnMessageText) {
        return array(
          'subject' => $subject,
          'body' => $message,
          'to' => $displayName,
          'html' => $html,
        );
      }
    }
  }
}
