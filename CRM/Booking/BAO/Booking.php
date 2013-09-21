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
    $adhocCharges = $params['adhoc_charges'];

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

      foreach ($resources as $key => $resource) {
        $slot = array(
          'version' => 3,
          'booking_id' => $bookingID,
          'config_id' => CRM_Utils_Array::value('configuration_id', $resource),
          'start' => CRM_Utils_Array::value('start_date', $resource),
          'end' => CRM_Utils_Array::value('end_date', $resource),
          'resource_id' =>  CRM_Utils_Array::value('resource_id', $resource),
          'quantity' => CRM_Utils_Array::value('quantity', $resource),
          'note' => CRM_Utils_Array::value('note', $resource),
        );
        $slotResult = civicrm_api('Slot', 'Create', $slot);
        $slotID =  CRM_Utils_Array::value('id', $slotResult);

        $subResources = $resource['sub_resources'];
        foreach($subResources as $subKey => $subResource){
          $subSlot = array(
            'version' => 3,
            'resource_id' =>  CRM_Utils_Array::value('resource_id', $subResource),
            'slot_id' => $slotID,
            'config_id' => CRM_Utils_Array::value('configuration_id', $subResource),
            'time_required' =>  CRM_Utils_Array::value('time_required', $subResource),
            'quantity' => CRM_Utils_Array::value('quantity', $subResource),
            'note' => CRM_Utils_Array::value('note', $subResource),
          );
          $subSlotResult = civicrm_api('SubSlot', 'Create', $subSlot);
        }
      }
      if($adhocCharges){
        $items = CRM_Utils_Array::value('items', $adhocCharges);
        foreach ($items as $key => $item) {
          $params = array(
            'version' => 3,
            'booking_id' =>  $bookingID,
            'item_id' => CRM_Utils_Array::value('id', $item),
            'quantity' => CRM_Utils_Array::value('quantity', $item),
          );
          civicrm_api('AdhocCharges', 'create', $params);
        }
      }
      return $booking;
    }catch (Exception $e) {
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
          //'trxn_id' =>  CRM_Utils_Array::value('trxn_id', $values),
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

          $lineItem['entity_table'] = "civicrm_booking_slot";
          $lineItem['entity_id'] = $slotID;

          $configId =  CRM_Utils_Array::value('config_id', $slot);
          $configResult = civicrm_api('ResourceConfigOption', 'get', array('version' => 3, 'id' => $configId));
          $config = CRM_Utils_Array::value('values', $configResult);
          $lineItem['label'] = CRM_Utils_Array::value('label', $config[$configId]);

          $unitPrice = CRM_Utils_Array::value('price', $config[$configId]);
          $lineItem['unit_price'] = $unitPrice;
          $qty = CRM_Utils_Array::value('quantity', $slot);

          $lineItem['qty'] = $qty;
          $lineItem['line_total'] =  $unitPrice * $qty;
          $lineItemResult = civicrm_api('LineItem', 'create', $lineItem);
          $result = civicrm_api('SubSlot', 'get', array('version' => 3 ,'slot_id' => $slotID));
          $subSlots = CRM_Utils_Array::value('values', $result);
          foreach ($subSlots as $subSlot) {

            $subSlotID = $subSlot['id'];

            $lineItem['entity_table'] = "civicrm_booking_sub_slot";
            $lineItem['entity_id'] = $subSlotID;
            $configId =  CRM_Utils_Array::value('config_id', $slot);
            $configResult = civicrm_api('ResourceConfigOption', 'get', array('version' => 3, 'id' => $configId));
            $config = CRM_Utils_Array::value('values', $configResult);
            $lineItem['label'] = CRM_Utils_Array::value('label', $config[$configId]);

            $unitPrice = CRM_Utils_Array::value('price', $config[$configId]);
            $lineItem['unit_price'] = $unitPrice;
            $qty = CRM_Utils_Array::value('quantity', $slot);

            $lineItem['qty'] = $qty;
            $lineItem['line_total'] =  $unitPrice * $qty;
            $lineItemResult = civicrm_api('LineItem', 'create', $lineItem);
          }
        }

        $adhocChargesResult = civicrm_api('AdhocCharges', 'get', array('version' => 3, 'booking_id' => $bookingID));
        $adhocChargesValues = CRM_Utils_Array::value('values', $adhocChargesResult);
        foreach ($adhocChargesValues as $id => $adhocCharges) {

          $lineItem['entity_table'] = "civicrm_booking_adhoc_charges";
          $lineItem['entity_id'] = $id;
          $itemId =  CRM_Utils_Array::value('item_id', $adhocCharges);
          $itemResult = civicrm_api('AdhocChargesItem', 'get', array('version' => 3, 'id' => $itemId));
          $itemValue = CRM_Utils_Array::value('values', $itemResult);
          $lineItem['label'] = CRM_Utils_Array::value('label', $itemValue[$itemId]);

          $unitPrice = CRM_Utils_Array::value('price', $itemValue[$itemId]);
          $lineItem['unit_price'] = $unitPrice;
          $qty = CRM_Utils_Array::value('quantity', $adhocCharges);

          $lineItem['qty'] = $qty;
          $lineItem['line_total'] =  $unitPrice * $qty;
          $lineItemResult = civicrm_api('LineItem', 'create', $lineItem);
        }

      }catch (Exception $e) {
          $transaction->rollback();
          CRM_Core_Error::fatal($e->getMessage());
      }
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

  /**
   * Process that send e-mails
   *
   * @return void
   * @access public
   */
  static function sendMail($contactID, &$values, $isTest = FALSE, $returnMessageText = FALSE) {

    $template = CRM_Core_Smarty::singleton();

    list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactID);

    //send email only when email is present
    if (isset($email) || $returnMessageText) {

      $tplParams = array(
        'email' => $email,
        //TODO:: build the booking tpl
      );
      dpr($contactID);
      $sendTemplateParams = array(
        'groupName' => 'msg_tpl_workflow_booking',
        'valueName' => 'booking_offline_receipt',
        'contactId' => $contactID,
        'isTest' => $isTest,
        'tplParams' => $tplParams,
        'PDFFilename' => 'bookingReceipt.pdf',
      );

      // address required during receipt processing (pdf and email receipt)
      //TODO:: add addresss
      if ($displayAddress = CRM_Utils_Array::value('address', $values)) {
        $sendTemplateParams['tplParams']['address'] = $displayAddress;
      }

      //TODO:: add line titem tpl params
      if ($lineItem = CRM_Utils_Array::value('lineItem', $values)) {
        $sendTemplateParams['tplParams']['lineItem'] = $lineItem;
        }
      }

     if ($returnMessageText) {
        list($sent, $subject, $message, $html) = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
          return array(
            'subject' => $subject,
            'body' => $message,
            'to' => $displayName,
            'html' => $html,
        );
      }
      else {
        dpr($email);
        //TODO: get from email from the system
        $sendTemplateParams['from'] = "erawat.chamanont@compucorp.co.uk". " <Erawat Chamanont>";
        $sendTemplateParams['toName'] = $displayName;
        $sendTemplateParams['toEmail'] = $email;
        //$sendTemplateParams['autoSubmitted'] = TRUE;
        //TODO:: get cc email from the system;
       // $sendTemplateParams['cc'] = CRM_Utils_Array::value('cc_confirm', 'cc@ccc');
        //TODO:: add bcc email
        //$sendTemplateParams['bcc'] = CRM_Utils_Array::value('bcc_confirm', 'bcc@bcc');
        CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);

      }

  }
}
