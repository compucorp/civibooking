<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_Booking_Info extends CRM_Booking_Form_Booking_Base {

  /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return ts('Booking Information');
  }


  function preProcess(){
    $this->_id = $this->get('id');
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      parent::preProcess();
    }
    $config = CRM_Core_Config::singleton();
    $currencySymbols = "";
    if(!empty($config->currencySymbols)){
      $currencySymbols = $config->currencySymbols;
    }else{
      $currencySymbols = $config->defaultCurrencySymbol;
    }
    $this->assign('currencySymbols', $currencySymbols);
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      $title = CRM_Core_DAO::getFieldValue('CRM_Booking_BAO_Booking', $this->_id, 'title', 'id');
      CRM_Utils_System::setTitle(ts('Edit Booking') . " - $title");
    }else{
      CRM_Utils_System::setTitle(ts('New Booking') );
    }
    self::registerScripts();
  }

  function buildQuickForm() {
    parent::buildQuickForm();

    $this->addElement('hidden', "primary_contact_select_id");
    $this->add('text', "primary_contact_id", ts('Primary contact'), array(), TRUE );

    $this->addElement('hidden', "secondary_contact_select_id");
    $this->add('text', "secondary_contact_id", ts('Secondary contact'));

    $this->add('text', 'po_no', ts('Purchase order number'));

    $bookingStatus =  CRM_Booking_BAO_Booking::buildOptions('status_id', 'create');
    $result = civicrm_api3(
      'OptionValue',
      'get',
      array(
        'option_group_name' => CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS,
        'name' => CRM_Booking_Utils_Constants::OPTION_VALUE_CANCELLED,
      )
    );
    $this->_cancelStatusId = CRM_Utils_Array::value('value', CRM_Utils_Array::value($result['id'], $result['values']));
    unset($bookingStatus[$this->_cancelStatusId]);
    $this->add('select', 'booking_status', ts('Booking status'),
      array('' => ts('- select -')) + $bookingStatus,
      TRUE,
      array()
    );

    $this->add('text', 'title', ts('Title'), array('size' => 80, 'maxlength' => 255), TRUE);
    $this->addDate('event_start_date', ts('Date booking made'), TRUE, array('formatType' => 'activityDateTime'));
    $this->add('textarea', 'description', ts('Description'));
    $this->add('textarea', 'note', ts('Note'));

    $this->add('text', 'enp', ts('Estimate number of participants'));
    $this->add('text', 'fnp', ts('Final number of participants'));

    $this->addElement('hidden', "resources");

    $buttons = array(
      array('type' => 'back',
        'name' => ts('<< Back'),
      ),
      array(
        'type' => 'submit',
        'name' => ts('Complete and Save'),
      ),
    );

    $this->addButtons($buttons);

    $this->addFormRule( array( 'CRM_Booking_Form_Booking_Info', 'formRule' ), $this );


  }

  static function formRule($params, $files, $context) {
    $errors = parent::rules($params, $files, $context);
    //make sure primary contact is selected
    $contactId = CRM_Utils_Array::value('primary_contact_select_id', $params);
    if(!$contactId){
      $errors['primary_contact_select_id'] = ts('This field is required.');
    }
    $selectResource = $context->controller->exportValues('SelectResource');
    $resources = json_decode($selectResource['resources'], true);
    $resourcesToValidate['resources'] = array();
    foreach ($resources as $key => $resource) {
      $resource['start'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('start_date', $resource));
      $resource['end'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $resource));
      $resourcesToValidate['resources'][$key] = $resource;
    }
    $result = civicrm_api3('Slot', 'validate', $resourcesToValidate);
    $values = $result['values'];
    if(!$values['is_valid']){
      $errors['resources'] = ts('Unfortunately one or more of your booking slots are clashing with another booking. You will need to edit your booking times to resolve this before you can save your booking. Please go back to the first page to edit your booking slots.');
    }
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      $defaults['primary_contact_select_id'] = CRM_Utils_Array::value('primary_contact_id', $this->_values);
      $displayName = CRM_Contact_BAO_Contact::displayName(CRM_Utils_Array::value('primary_contact_id', $this->_values));
      $defaults['primary_contact_id'] =  CRM_Utils_Array::value('primary_contact_id', $this->_values) . "::" . $displayName;

      $defaults['secondary_contact_select_id'] = CRM_Utils_Array::value('secondary_contact_id', $this->_values);
      $displayName = CRM_Contact_BAO_Contact::displayName(CRM_Utils_Array::value('secondary_contact_id', $this->_values));
      $defaults['secondary_contact_id'] =  CRM_Utils_Array::value('secondary_contact_id', $this->_values) . "::" . $displayName;

      $defaults['title'] = CRM_Utils_Array::value('title', $this->_values);
      $defaults['po_no'] = CRM_Utils_Array::value('po_no', $this->_values);
      $defaults['booking_status'] =  CRM_Utils_Array::value('booking_status_id', $this->_values);
      $defaults['event_start_date'] = CRM_Utils_Array::value('booking_date', $this->_values);
      list($defaults['event_start_date'], $defaults['event_start_date_time']) = CRM_Utils_Date::setDateDefaults($defaults['event_start_date'], 'activityDateTime');
      $defaults['description'] =  CRM_Utils_Array::value('description', $this->_values);
      $defaults['note'] =  CRM_Utils_Array::value('note', $this->_values);
      $defaults['enp'] = CRM_Utils_Array::value('participants_estimate', $this->_values);
      $defaults['fnp'] =  CRM_Utils_Array::value('participants_actual', $this->_values);
    }else{
      list($defaults['event_start_date'], $defaults['event_start_date_time']) = CRM_Utils_Date::setDateDefaults(date("Y-m-d H:i:s"), 'activityDateTime');

    }
    $addSubResourcePage = $this->controller->exportValues('AddSubResource');
    $defaults['total_amount'] = $addSubResourcePage['total_price']; //use the amount that passing from the form
    $amountToFloat = floatval($defaults['total_amount']);
    $defaults['total_amount'] = round( $amountToFloat, 2, PHP_ROUND_HALF_UP);
    return $defaults;
  }

  function postProcess() {
    $bookingInfo = $this->exportValues();
    $selectResource = $this->controller->exportValues('SelectResource');
    $addSubResource = $this->controller->exportValues('AddSubResource');
    $resourcesValue = json_decode($selectResource['resources'], true);
    $subResourcesValue = json_decode($addSubResource['sub_resources'], true);
    $subResources = $subResourcesValue['sub_resources'];

    //Build resources array for passing to Booking APIs
    $resources = array();
    foreach ($resourcesValue as $key => $resource) {
      //Remove element that used in DTHMLX as we do not need them.
      unset($resource['id']);
      unset($resource['label']);
      unset($resource['text']);
      unset($resource['readonly']);
      $resource['start_date'] = $resource['start_date'];
      $resource['end_date'] = $resource['end_date'];
      $resource['sub_resources'] = array();
      if(isset($subResources)){
       foreach ($subResources as $subKey => $subResource) {
          if($key == $subResource['parent_ref_id']){
            $subResourceTmp['resource_id'] = $subResource['resource']['id'];
            $subResourceTmp['configuration_id'] = $subResource['configuration']['id'];
            $subResourceTmp['quantity'] = $subResource['quantity'];
            $subResourceTmp['time_required'] = $subResource['time_required'];
            $subResourceTmp['note'] = $subResource['note'];
            $subResourceTmp['price_estimate'] = $subResource['price_estimate'];
            array_push($resource['sub_resources'], $subResourceTmp);
            unset($subResources[$subKey]);
          }
        }
      }
      array_push($resources,  $resource);
    }

    $adhocCharges =  CRM_Utils_Array::value('adhoc_charges', $subResourcesValue);

    $booking = array();
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      $booking['id'] = $this->_id;
    }

    $booking['primary_contact_id'] = CRM_Utils_Array::value('primary_contact_select_id', $bookingInfo);
    $booking['secondary_contact_id'] = CRM_Utils_Array::value('secondary_contact_select_id', $bookingInfo);
    $booking['po_number'] = CRM_Utils_Array::value('po_no', $bookingInfo);
    $booking['status_id'] = CRM_Utils_Array::value('booking_status', $bookingInfo);
    $booking['title'] = CRM_Utils_Array::value('title', $bookingInfo);
    $booking['description'] = CRM_Utils_Array::value('description', $bookingInfo);
    $booking['note'] = CRM_Utils_Array::value('note', $bookingInfo);



    $booking['booking_date'] = CRM_Utils_Date::processDate(
      CRM_Utils_Array::value('event_start_date', $bookingInfo),
      CRM_Utils_Array::value('event_start_date_time', $bookingInfo)
    );

    $booking['discount_amount'] = CRM_Utils_Array::value('discount_amount', $addSubResource);
    $booking['total_amount'] = CRM_Utils_Array::value('total_price', $addSubResource);
    $amountToFloat = floatval($booking['total_amount']);
    $booking['total_amount'] = round( $amountToFloat, 2, PHP_ROUND_HALF_UP);
    //add adhoc charge
    $booking['adhoc_charges_note'] = CRM_Utils_Array::value('note', $adhocCharges);

    $booking['participants_estimate'] = CRM_Utils_Array::value('enp', $bookingInfo);
    $booking['participants_actual'] = CRM_Utils_Array::value('fnp', $bookingInfo);

    $now  = date('YmdHis');
    $session =& CRM_Core_Session::singleton( );
    $booking['created_by'] =  $session->get( 'userID' );
    $booking['created_date'] = $now;
    $booking['updated_by'] = $session->get( 'userID' );
    $booking['updated_date'] = $now;

    //retrieve booking_start_date, booking_end_date from all slots
    $dates = array();
    foreach ($resources as $key => $slot) {
        array_push($dates, CRM_Utils_Array::value('start_date', $slot));
        array_push($dates, CRM_Utils_Array::value('end_date', $slot));
    }
    sort($dates);
    $bookingStartDate = $dates[0];
    $bookingEndDate = $dates[count($dates)-1];


    $booking['booking_start_date'] = CRM_Utils_Date::processDate($bookingStartDate);
    $booking['booking_end_date'] = CRM_Utils_Date::processDate($bookingEndDate);

    //make sure we create everything in one transaction, not too nice but it does the job
    $transaction = new CRM_Core_Transaction();

    try{
      $result = civicrm_api3('Booking', 'Create', $booking);
      $bookingID = CRM_Utils_Array::value('id', $result);
      $booking =  CRM_Utils_Array::value($bookingID, CRM_Utils_Array::value('values', $result));
      $this->_id = $bookingID; //make sure we have the id on create mode
      $this->_values = $booking;

      //Now we process slots/subslots and adhoc charges
      if($this->_action == CRM_Core_Action::UPDATE){ //booking id was passed from the form so we are on edit mode
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
        if($this->_action == CRM_Core_Action::UPDATE){
          list($isExist, $currentID) = CRM_Booking_BAO_Slot::findExistingSlot($slot, $currentSlots);
          if($isExist){
            $slot['id'] = $currentID;
          }
        }
        $slotResult = civicrm_api3('Slot', 'create', $slot);
        $slotID =  CRM_Utils_Array::value('id', $slotResult);
        array_push($newSlotIds, $slotID);

        if($this->_action == CRM_Core_Action::UPDATE){
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
          if($this->_action == CRM_Core_Action::UPDATE){
            list($isExist, $currentSubSlotId) =  CRM_Booking_BAO_SubSlot::findExistingSubSlot($subSlot, $currentSubSlots);
            if($isExist){
              $subSlot['id'] = $currentSubSlotId;
            }
          }
          $subSlotResult = civicrm_api3('SubSlot', 'Create', $subSlot);
          $subSlotID =  CRM_Utils_Array::value('id', $subSlotResult);
          array_push($newSubSlotIds, $subSlotID);
        }
        if($this->_action == CRM_Core_Action::UPDATE){ //remove subslots that have been removed
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
      if($this->_action == CRM_Core_Action::UPDATE){ //remove all slots that have been removed
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
        if($this->_action == CRM_Core_Action::UPDATE){
          $result = civicrm_api3('AdhocCharges', 'get', array('booking_id' => $bookingID, 'is_deleted' => 0));
          $currentAdhocCharges = $result['values'];
        }
        // fixed bug of CVB-94
        // Ad-hoc charges - cannot save
        if(!is_null(CRM_Utils_Array::value('items', $adhocCharges))){
          $items = array_filter(CRM_Utils_Array::value('items', $adhocCharges));
          $newAdhocChargesIds = array();
           foreach ($items as $key => $item) {
            $params = array(
              'booking_id' =>  $bookingID,
              'item_id' => CRM_Utils_Array::value('item_id', $item),
              'quantity' => CRM_Utils_Array::value('quantity', $item),
            );
            if($this->_action == CRM_Core_Action::UPDATE){
              list($isExist, $currentAdhocChargesId) =  CRM_Booking_BAO_AdhocCharges::findExistingAdhocCharges($params, $currentAdhocCharges);
              if($isExist){
                $params['id'] =  $currentAdhocChargesId;
              }
            }
            $result = civicrm_api3('AdhocCharges', 'create', $params);
            $adhocChargesId =  CRM_Utils_Array::value('id', $result);
            array_push($newAdhocChargesIds, $adhocChargesId);
          }
        }

        if($this->_action == CRM_Core_Action::UPDATE){ //remove  adhoc charges that have been removed
          $adhocChargesToBeRemoved = array();
          foreach ($currentAdhocCharges as $key => $adc) {
            if(!in_array($key, $newAdhocChargesIds)){
              $adhocChargesToBeRemoved[$key] = $adc;
            }
          }
          if(!empty($adhocChargesToBeRemoved)){
            foreach ($adhocChargesToBeRemoved as $key => $adc) {
              civicrm_api3('AdhocCharges', 'delete', array('id' => $key));
            }
          }
        }
      }

      //End process
      parent::postProcess();

      $cid = CRM_Utils_Array::value('primary_contact_select_id', $bookingInfo);
      // user context
      $url = CRM_Utils_System::url('civicrm/contact/view/booking',
         "reset=1&id=$bookingID&cid=$cid&action=view"
      );
      CRM_Core_Session::setStatus($booking['title'], ts('Saved'), 'success');
      CRM_Utils_System::redirect( $url);
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

  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()

      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/booking.css', 92, 'page-header')
      ->addScriptFile('civicrm', 'packages/backbone/json2.js', 100, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)

      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/booking-info/app.js', 150, 'html-header')
      //->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/booking-info/entities.js', 160, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/booking-info/view.js', 170, 'html-header');

    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath('uk.co.compucorp.civicrm.booking') . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Booking/tpl/booking-info/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add(array(
        'template' => $fileName,
      ));
    }

  }


}
