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
          'trxn_id' =>  CRM_Utils_Array::value('trxn_id', $values),
        );
         //$result = civicrm_api('Contribution', 'create', $params);
        //call contribution directly as if the trxn_id exist we cannot continue
        $contribution = CRM_Contribute_BAO_Contribution::add($params);
        if($contribution instanceof CRM_Core_Error){
          throw new Exception($contribution->_errors[0]['message']);
        }

        $payment = array('booking_id' => $bookingID, 'contribution_id' => $contribution->id);
        CRM_Booking_BAO_Payment::create($payment);

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
   * Function to send the emails
   *
   * @param array   $contactIDs        contact ids
   * @param array   $values            associated array of fields
   * @param boolean $isTest            if in test mode
   * @param boolean $returnMessageText return the message text instead of sending the mail
   *
   * @return void
   * @access public
   * @static
   */
  static function sendMail($contactIDs, &$values, $isTest = FALSE, $returnMessageText = FALSE, $fieldTypes = NULL) {
    $gIds = $params = array();
    $email = NULL;
    if (isset($values['custom_pre_id'])) {
      $preProfileType = CRM_Core_BAO_UFField::getProfileType($values['custom_pre_id']);
      if ($preProfileType == 'Membership' && CRM_Utils_Array::value('membership_id', $values)) {
        $params['custom_pre_id'] = array(
          array(
            'member_id',
            '=',
            $values['membership_id'],
            0,
            0,
          ),
        );
      }
      elseif ($preProfileType == 'Contribution' && CRM_Utils_Array::value('contribution_id', $values)) {
        $params['custom_pre_id'] = array(
          array(
            'contribution_id',
            '=',
            $values['contribution_id'],
            0,
            0,
          ),
        );
      }

      $gIds['custom_pre_id'] = $values['custom_pre_id'];
    }

    if (isset($values['custom_post_id'])) {
      $postProfileType = CRM_Core_BAO_UFField::getProfileType($values['custom_post_id']);
      if ($postProfileType == 'Membership' && CRM_Utils_Array::value('membership_id', $values)) {
        $params['custom_post_id'] = array(
          array(
            'member_id',
            '=',
            $values['membership_id'],
            0,
            0,
          ),
        );
      }
      elseif ($postProfileType == 'Contribution' && CRM_Utils_Array::value('contribution_id', $values)) {
        $params['custom_post_id'] = array(
          array(
            'contribution_id',
            '=',
            $values['contribution_id'],
            0,
            0,
          ),
        );
      }

      $gIds['custom_post_id'] = $values['custom_post_id'];
    }

    if (CRM_Utils_Array::value('is_for_organization', $values)) {
      if (CRM_Utils_Array::value('membership_id', $values)) {
        $params['onbehalf_profile'] = array(
          array(
            'member_id',
            '=',
            $values['membership_id'],
            0,
            0,
          ),
        );
      }
      elseif (CRM_Utils_Array::value('contribution_id', $values)) {
        $params['onbehalf_profile'] = array(
          array(
            'contribution_id',
            '=',
            $values['contribution_id'],
            0,
            0,
          ),
        );
      }
    }

    //check whether it is a test drive
    if ($isTest && !empty($params['custom_pre_id'])) {
      $params['custom_pre_id'][] = array(
        'contribution_test',
        '=',
        1,
        0,
        0,
      );
    }

    if ($isTest && !empty($params['custom_post_id'])) {
      $params['custom_post_id'][] = array(
        'contribution_test',
        '=',
        1,
        0,
        0,
      );
    }

    if (!$returnMessageText && !empty($gIds)) {
      //send notification email if field values are set (CRM-1941)
      foreach ($gIds as $key => $gId) {
        if ($gId) {
          $email = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_UFGroup', $gId, 'notify');
          if ($email) {
            $val = CRM_Core_BAO_UFGroup::checkFieldsEmptyValues($gId, $contactID, CRM_Utils_Array::value($key, $params), true );
            CRM_Core_BAO_UFGroup::commonSendMail($contactID, $val);
          }
        }
      }
    }

    if ( CRM_Utils_Array::value('is_email_receipt', $values) ||
      CRM_Utils_Array::value('onbehalf_dupe_alert', $values) ||
      $returnMessageText
    ) {
      $template = CRM_Core_Smarty::singleton();

      // get the billing location type
      if (!array_key_exists('related_contact', $values)) {
        $locationTypes = CRM_Core_PseudoConstant::get('CRM_Core_DAO_Address', 'location_type_id');
        $billingLocationTypeId = array_search('Billing', $locationTypes);
      }
      else {
        // presence of related contact implies onbehalf of org case,
        // where location type is set to default.
        $locType = CRM_Core_BAO_LocationType::getDefault();
        $billingLocationTypeId = $locType->id;
      }

      if (!array_key_exists('related_contact', $values)) {
        list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactID, FALSE, $billingLocationTypeId);
      }
      // get primary location email if no email exist( for billing location).
      if (!$email) {
        list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactID);
      }
      if (empty($displayName)) {
        list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactID);
      }

      //for display profile need to get individual contact id,
      //hence get it from related_contact if on behalf of org true CRM-3767
      //CRM-5001 Contribution/Membership:: On Behalf of Organization,
      //If profile GROUP contain the Individual type then consider the
      //profile is of Individual ( including the custom data of membership/contribution )
      //IF Individual type not present in profile then it is consider as Organization data.
      $userID = $contactID;
      if ($preID = CRM_Utils_Array::value('custom_pre_id', $values)) {
        if (CRM_Utils_Array::value('related_contact', $values)) {
          $preProfileTypes = CRM_Core_BAO_UFGroup::profileGroups($preID);
          if (in_array('Individual', $preProfileTypes) || in_array('Contact', $postProfileTypes)) {
            //Take Individual contact ID
            $userID = CRM_Utils_Array::value('related_contact', $values);
          }
        }
        self::buildCustomDisplay($preID, 'customPre', $userID, $template, $params['custom_pre_id']);
      }
      $userID = $contactID;
      if ($postID = CRM_Utils_Array::value('custom_post_id', $values)) {
        if (CRM_Utils_Array::value('related_contact', $values)) {
          $postProfileTypes = CRM_Core_BAO_UFGroup::profileGroups($postID);
          if (in_array('Individual', $postProfileTypes) || in_array('Contact', $postProfileTypes)) {
            //Take Individual contact ID
            $userID = CRM_Utils_Array::value('related_contact', $values);
          }
        }
        self::buildCustomDisplay($postID, 'customPost', $userID, $template, $params['custom_post_id']);
      }

      $title = isset($values['title']) ? $values['title'] : CRM_Contribute_PseudoConstant::contributionPage($values['contribution_page_id']);

      // set email in the template here
      $tplParams = array(
        'email' => $email,
        'receiptFromEmail' => CRM_Utils_Array::value('receipt_from_email', $values),
        'contactID' => $contactID,
        'displayName' => $displayName,
        'contributionID' => CRM_Utils_Array::value('contribution_id', $values),
        'contributionOtherID' => CRM_Utils_Array::value('contribution_other_id', $values),
        'membershipID' => CRM_Utils_Array::value('membership_id', $values),
        // CRM-5095
        'lineItem' => CRM_Utils_Array::value('lineItem', $values),
        // CRM-5095
        'priceSetID' => CRM_Utils_Array::value('priceSetID', $values),
        'title' => $title,
        'isShare' => CRM_Utils_Array::value('is_share', $values),
      );

      if ($contributionTypeId = CRM_Utils_Array::value('financial_type_id', $values)) {
        $tplParams['contributionTypeId'] = $contributionTypeId;
        $tplParams['contributionTypeName'] = CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_FinancialType',
          $contributionTypeId);
      }

      if ($contributionPageId = CRM_Utils_Array::value('id', $values)) {
        $tplParams['contributionPageId'] = $contributionPageId;
      }

      // address required during receipt processing (pdf and email receipt)
      if ($displayAddress = CRM_Utils_Array::value('address', $values)) {
        $tplParams['address'] = $displayAddress;
      }

      // CRM-6976
      $originalCCReceipt = CRM_Utils_Array::value('cc_receipt', $values);

      // cc to related contacts of contributor OR the one who
      // signs up. Is used for cases like - on behalf of
      // contribution / signup ..etc
      if (array_key_exists('related_contact', $values)) {
        list($ccDisplayName, $ccEmail) = CRM_Contact_BAO_Contact_Location::getEmailDetails($values['related_contact']);
        $ccMailId = "{$ccDisplayName} <{$ccEmail}>";

        $values['cc_receipt'] = CRM_Utils_Array::value('cc_receipt', $values) ? ($values['cc_receipt'] . ',' . $ccMailId) : $ccMailId;

        // reset primary-email in the template
        $tplParams['email'] = $ccEmail;

        $tplParams['onBehalfName'] = $displayName;
        $tplParams['onBehalfEmail'] = $email;

        $ufJoinParams = array(
          'module' => 'onBehalf',
          'entity_table' => 'civicrm_contribution_page',
          'entity_id' => $values['id'],
        );
        $OnBehalfProfile = CRM_Core_BAO_UFJoin::getUFGroupIds($ufJoinParams);
        $profileId = $OnBehalfProfile[0];
        $userID = $contactID;
        self::buildCustomDisplay($profileId, 'onBehalfProfile', $userID, $template, $params['onbehalf_profile'], $fieldTypes);
      }

      // use either the contribution or membership receipt, based on whether it’s a membership-related contrib or not
      $sendTemplateParams = array(
        'groupName' => $tplParams['membershipID'] ? 'msg_tpl_workflow_membership' : 'msg_tpl_workflow_contribution',
        'valueName' => $tplParams['membershipID'] ? 'membership_online_receipt' : 'contribution_online_receipt',
        'contactId' => $contactID,
        'tplParams' => but ,
        'isTest' => $isTest,
        'PDFFilename' => 'receipt.pdf',
      );

      if ($returnMessageText) {
        list($sent, $subject, $message, $html) = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
        return array(
          'subject' => $subject,
          'body' => $message,
          'to' => $displayName,
          'html' => $html,
        );
      }

      if ($values['is_email_receipt']) {
        $sendTemplateParams['from'] = CRM_Utils_Array::value('receipt_from_name', $values) . ' <' . $values['receipt_from_email'] . '>';
        $sendTemplateParams['toName'] = $displayName;
        $sendTemplateParams['toEmail'] = $email;
        $sendTemplateParams['cc'] = CRM_Utils_Array::value('cc_receipt', $values);
        $sendTemplateParams['bcc'] = CRM_Utils_Array::value('bcc_receipt', $values);
        list($sent, $subject, $message, $html) = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
      }

      // send duplicate alert, if dupe match found during on-behalf-of processing.
      if (CRM_Utils_Array::value('onbehalf_dupe_alert', $values)) {
        $sendTemplateParams['groupName'] = 'msg_tpl_workflow_contribution';
        $sendTemplateParams['valueName'] = 'contribution_dupalert';
        $sendTemplateParams['from'] = ts('Automatically Generated') . " <{$values['receipt_from_email']}>";
        $sendTemplateParams['toName'] = CRM_Utils_Array::value('receipt_from_name', $values);
        $sendTemplateParams['toEmail'] = CRM_Utils_Array::value('receipt_from_email', $values);
        $sendTemplateParams['tplParams']['onBehalfID'] = $contactID;
        $sendTemplateParams['tplParams']['receiptMessage'] = $message;

        // fix cc and reset back to original, CRM-6976
        $sendTemplateParams['cc'] = $originalCCReceipt;

        CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
      }
    }
  }



}
