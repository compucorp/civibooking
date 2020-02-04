<?php
use CRM_Booking_ExtensionUtil as E;

/**
 * Form controller class
 */
class CRM_Booking_Form_Booking_Info extends CRM_Booking_Form_Booking_Base {

  use CRM_Core_Form_EntityFormTrait;

  /**
   * Fields for the entity to be assigned to the template.
   *
   * Fields may have keys
   *  - name (required to show in tpl from the array)
   *  - description (optional, will appear below the field)
   *  - not-auto-addable - this class will not attempt to add the field using addField.
   *    (this will be automatically set if the field does not have html in it's metadata
   *    or is not a core field on the form's entity).
   *  - help (optional) add help to the field - e.g ['id' => 'id-source', 'file' => 'CRM/Contact/Form/Contact']]
   *  - template - use a field specific template to render this field
   *  - required
   * @var array
   */
  protected $entityFields = [];

  /**
   * Explicitly declare the entity api name.
   */
  public function getDefaultEntity() {
    return 'Booking';
  }

  /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return E::ts('Booking Information');
  }

  public function preProcess() {
    $this->_id = $this->get('id');
    if ($this->_id && $this->_action == CRM_Core_Action::UPDATE) {
      parent::preProcess();
    }
    if ($this->_id && $this->_action == CRM_Core_Action::UPDATE) {
      $title = CRM_Core_DAO::getFieldValue('CRM_Booking_BAO_Booking', $this->_id, 'title', 'id');
      CRM_Utils_System::setTitle(E::ts('Edit Booking') . " - $title");
    }
    else {
      CRM_Utils_System::setTitle(E::ts('New Booking'));
    }
    self::registerScripts();
  }

  public function buildQuickForm() {
    self::buildQuickEntityForm();
    parent::buildQuickForm();

    $this->addEntityRef("primary_contact_id", E::ts('Primary contact'), ['create' => TRUE], TRUE );
    $this->addEntityRef("secondary_contact_id", E::ts('Secondary contact'), ['create' => TRUE]);
    $this->add('text', 'po_no', E::ts('Purchase order number'));

    $bookingStatus =  CRM_Booking_BAO_Booking::buildOptions('status_id', 'create');
    $result = civicrm_api3(
      'OptionValue',
      'get',
      [
        'option_group_name' => CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS,
        'name' => CRM_Booking_Utils_Constants::OPTION_VALUE_CANCELLED,
      ]
    );
    $this->_cancelStatusId = CRM_Utils_Array::value('value', CRM_Utils_Array::value($result['id'], $result['values']));
    unset($bookingStatus[$this->_cancelStatusId]);
    $this->add('select', 'booking_status', E::ts('Booking status'),
      ['' => E::ts('- select -')] + $bookingStatus,
      TRUE,
      []
    );

    $this->add('text', 'title', E::ts('Title'), ['size' => 50, 'maxlength' => 255], TRUE);
    $this->add('datepicker', 'event_start_date', E::ts('Date booking made'), ['formatType' => 'activityDateTime'], TRUE);
    $this->add('textarea', 'description', E::ts('Description'));
    $this->add('textarea', 'note', E::ts('Note'));

    $this->add('text', 'enp', E::ts('Estimate number of participants'));
    $this->add('text', 'fnp', E::ts('Final number of participants'));

    $this->addElement('hidden', "resources");

    $buttons = [
      [
        'type' => 'back',
        'name' => E::ts('<< Back'),
      ],
      [
        'type' => 'submit',
        'name' => E::ts('Complete and Save'),
      ],
    ];

    $this->addButtons($buttons);
    $this->addFormRule(['CRM_Booking_Form_Booking_Info', 'formRule'], $this);
    $this->add('text', 'currencySymbol', E::ts('Currency'), ['disabled' => 'disabled']);
  }

  public static function formRule($params, $files, $context) {
    $errors = parent::rules($params, $files, $context);
    // make sure primary contact is selected
    $contactId = CRM_Utils_Array::value('primary_contact_id', $params);
    if (!is_numeric($contactId)) {
      $errors['primary_contact_id'] = E::ts('This field is required.');
    }
    $selectResource = $context->controller->exportValues('SelectResource');
    $resources = json_decode($selectResource['resources'], true) ?? [];
    $resourcesToValidate['resources'] = [];
    foreach ($resources as $key => $resource) {
      $resource['start'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('start_date', $resource));
      $resource['end'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $resource));
      $resourcesToValidate['resources'][$key] = $resource;
    }
    $result = civicrm_api3('Slot', 'validate', $resourcesToValidate);
    $values = $result['values'];
    if (!$values['is_valid']) {
      $errors['resources'] = E::ts('Unfortunately one or more of your booking slots are clashing with another booking. You will need to edit your booking times to resolve this before you can save your booking. Please go back to the first page to edit your booking slots.');
    }
    return empty($errors) ? TRUE : $errors;
  }

  public function setDefaultValues() {
    // prevent quickforms from filling total_amount with value submitted by
    // Back action - default value, which is filled correctly will be used instead
    unset($this->_submitValues['total_amount']);
    $defaults = parent::setDefaultValues();
    if ($this->_id && $this->_action == CRM_Core_Action::UPDATE) {
      $defaults['primary_contact_id'] = CRM_Utils_Array::value('primary_contact_id', $this->_values);
      $defaults['secondary_contact_id'] = CRM_Utils_Array::value('secondary_contact_id', $this->_values);

      $defaults['title'] = CRM_Utils_Array::value('title', $this->_values);
      $defaults['po_no'] = CRM_Utils_Array::value('po_number', $this->_values);
      $defaults['booking_status'] =  CRM_Utils_Array::value('booking_status_id', $this->_values);
      $defaults['event_start_date'] = CRM_Utils_Array::value('booking_date', $this->_values);
      $defaults['description'] =  CRM_Utils_Array::value('description', $this->_values);
      $defaults['note'] =  CRM_Utils_Array::value('note', $this->_values);
      $defaults['enp'] = CRM_Utils_Array::value('participants_estimate', $this->_values);
      $defaults['fnp'] =  CRM_Utils_Array::value('participants_actual', $this->_values);
    }
    else {
      $defaults['event_start_date'] = date('Y-m-d H:i:s');
    }
    $addSubResourcePage = $this->controller->exportValues('AddSubResource');
    $defaults['total_amount'] = $addSubResourcePage['total_price']; //use the amount that passing from the form
    $amountToFloat = floatval($defaults['total_amount']);
    $defaults['total_amount'] = round( $amountToFloat, 2, PHP_ROUND_HALF_UP);
    $config = CRM_Core_Config::singleton();
    if (!empty($config->currencySymbols)) {
      $currencySymbol = $config->currencySymbols;
    }
    else {
      $currencySymbol = $config->defaultCurrencySymbol;
    }
    $defaults['currencySymbol'] = $currencySymbol;
    return $defaults;
  }

  public function postProcess() {
    $bookingInfo = $this->exportValues();
    $selectResource = $this->controller->exportValues('SelectResource');
    $addSubResource = $this->controller->exportValues('AddSubResource');
    $resourcesValue = json_decode($selectResource['resources'], true) ?? [];
    $subResourcesValue = json_decode($addSubResource['sub_resources'], true);
    $subResources = $subResourcesValue['sub_resources'];

    //Build resources array for passing to Booking APIs
    $resources = [];
    foreach ($resourcesValue as $key => $resource) {
      //Remove element that used in DTHMLX as we do not need them.
      unset($resource['id']);
      unset($resource['label']);
      unset($resource['text']);
      unset($resource['readonly']);
      $resource['sub_resources'] = [];
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

    if (empty($resources)) {
      $qfKey = CRM_Utils_Request::retrieveValue('qfKey', 'String');
      $redirectUrl = CRM_Utils_System::url('civicrm/booking/add', ['qfKey' => $qfKey]);
      CRM_Core_Error::statusBounce(E::ts('You must select a time for at least one resource to make a booking.'), $redirectUrl, E::ts('No resources selected!'));
    }

    $adhocCharges =  CRM_Utils_Array::value('adhoc_charges', $subResourcesValue);

    $booking = [];
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      $booking['id'] = $this->_id;
    }

    $booking['primary_contact_id'] = CRM_Utils_Array::value('primary_contact_id', $bookingInfo);
    $booking['secondary_contact_id'] = CRM_Utils_Array::value('secondary_contact_id', $bookingInfo);
    $booking['po_number'] = CRM_Utils_Array::value('po_no', $bookingInfo);
    $booking['status_id'] = CRM_Utils_Array::value('booking_status', $bookingInfo);
    $booking['title'] = CRM_Utils_Array::value('title', $bookingInfo);
    $booking['description'] = CRM_Utils_Array::value('description', $bookingInfo);
    $booking['note'] = CRM_Utils_Array::value('note', $bookingInfo);
    $booking['booking_date'] = CRM_Utils_Array::value('event_start_date', $bookingInfo);
    $booking['discount_amount'] = CRM_Utils_Array::value('discount_amount', $addSubResource);
    $booking['total_amount'] = CRM_Utils_Array::value('total_price', $addSubResource);
    $amountToFloat = floatval($booking['total_amount']);
    $booking['total_amount'] = round( $amountToFloat, 2, PHP_ROUND_HALF_UP);
    //add adhoc charge
    $booking['adhoc_charges_note'] = CRM_Utils_Array::value('note', $adhocCharges);

    $booking['participants_estimate'] = CRM_Utils_Array::value('enp', $bookingInfo);
    $booking['participants_actual'] = CRM_Utils_Array::value('fnp', $bookingInfo);

    $booking['created_by'] = $booking['updated_by'] = CRM_Core_Session::getLoggedInContactID();
    $booking['created_date'] = $booking['updated_date'] = date('YmdHis');
    foreach ($bookingInfo as $key => $value) {
      if (substr($key, 0, 7) === 'custom_') {
        $booking[$key] = $value;
      }
    }

    //retrieve booking_start_date, booking_end_date from all slots
    $dates = [];
    foreach ($resources as $key => $slot) {
      array_push($dates, CRM_Utils_Array::value('start_date', $slot));
      array_push($dates, CRM_Utils_Array::value('end_date', $slot));
    }
    sort($dates);
    $booking['booking_start_date'] = reset($dates);
    $booking['booking_end_date'] = end($dates);

    //make sure we create everything in one transaction, not too nice but it does the job
    $transaction = new CRM_Core_Transaction();

    try {
      $result = civicrm_api3('Booking', 'Create', $booking);
      $bookingID = CRM_Utils_Array::value('id', $result);
      $booking =  CRM_Utils_Array::value($bookingID, CRM_Utils_Array::value('values', $result));
      $this->_id = $bookingID; //make sure we have the id on create mode
      $this->_values = $booking;

      //Now we process slots/subslots and adhoc charges
      if ($this->_action == CRM_Core_Action::UPDATE) { //booking id was passed from the form so we are on edit mode
        $currentSlots = CRM_Booking_BAO_Slot::getBookingSlot($bookingID);
      }
      $newSlotIds = [];
      $newSubSlotIds = [];
      foreach ($resources as $key => $resource) {
        $slot = [
          'booking_id' => $bookingID,
          'config_id' => CRM_Utils_Array::value('configuration_id', $resource),
          'start' => CRM_Utils_Date::processDate(CRM_Utils_Array::value('start_date', $resource)),
          'end' => CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $resource)),
          'resource_id' =>  CRM_Utils_Array::value('resource_id', $resource),
          'quantity' => CRM_Utils_Array::value('quantity', $resource),
          'note' => CRM_Utils_Array::value('note', $resource),
        ];
        if ($this->_action == CRM_Core_Action::UPDATE) {
          list($isExist, $currentID) = CRM_Booking_BAO_Slot::findExistingSlot($slot, $currentSlots);
          if ($isExist) {
            $slot['id'] = $currentID;
          }
        }
        $slotResult = civicrm_api3('Slot', 'create', $slot);
        $slotID =  CRM_Utils_Array::value('id', $slotResult);
        array_push($newSlotIds, $slotID);

        if ($this->_action == CRM_Core_Action::UPDATE) {
          $currentSubSlots = CRM_Booking_BAO_SubSlot::getSubSlotSlot($slotID);
        }
        $subResources = $resource['sub_resources'];
        foreach ($subResources as $subKey => $subResource) {
          $subSlot = [
            'resource_id' =>  CRM_Utils_Array::value('resource_id', $subResource),
            'slot_id' => $slotID,
            'config_id' => CRM_Utils_Array::value('configuration_id', $subResource),
            'time_required' =>  CRM_Utils_Date::processDate(CRM_Utils_Array::value('time_required', $subResource)),
            'quantity' => CRM_Utils_Array::value('quantity', $subResource),
            'note' => CRM_Utils_Array::value('note', $subResource),
          ];
          if ($this->_action == CRM_Core_Action::UPDATE) {
            list($isExist, $currentSubSlotId) = CRM_Booking_BAO_SubSlot::findExistingSubSlot($subSlot, $currentSubSlots);
            if ($isExist) {
              $subSlot['id'] = $currentSubSlotId;
            }
          }
          $subSlotResult = civicrm_api3('SubSlot', 'Create', $subSlot);
          $subSlotID =  CRM_Utils_Array::value('id', $subSlotResult);
          array_push($newSubSlotIds, $subSlotID);
        }
        if ($this->_action == CRM_Core_Action::UPDATE) { //remove subslots that have been removed
          $subSlotsToBeRemoved = [];
          foreach ($currentSubSlots as $key => $currentSubSlot) {
            if (!in_array($key, $newSubSlotIds)) {
              $subSlotsToBeRemoved[$key] = $currentSubSlot;
            }
          }
          if (!empty($subSlotsToBeRemoved)) {
            foreach ($subSlotsToBeRemoved as $key => $slot) {
              civicrm_api3('SubSlot', 'delete', ['id' => $key]);
            }
          }
        }
      }
      if ($this->_action == CRM_Core_Action::UPDATE) { //remove all slots that have been removed
        $slotsToBeRemoved = [];
        foreach ($currentSlots as $key => $currentSlot) {
          if(!in_array($key, $newSlotIds)){
            $slotsToBeRemoved[$key] = $currentSlot;
          }
        }
        if (!empty($slotsToBeRemoved)) {
          foreach ($slotsToBeRemoved as $key => $slot) {
            civicrm_api3('Slot', 'delete', ['id' => $key]);
          }
        }
      }
      if ($adhocCharges) {
        if ($this->_action == CRM_Core_Action::UPDATE) {
          $result = civicrm_api3('AdhocCharges', 'get', ['booking_id' => $bookingID, 'is_deleted' => 0]);
          $currentAdhocCharges = $result['values'];
        }
        // fixed bug of CVB-94
        // Ad-hoc charges - cannot save
        if (!is_null(CRM_Utils_Array::value('items', $adhocCharges))) {
          $items = array_filter(CRM_Utils_Array::value('items', $adhocCharges));
          $newAdhocChargesIds = [];
          foreach ($items as $key => $item) {
            $params = [
              'booking_id' =>  $bookingID,
              'item_id' => CRM_Utils_Array::value('item_id', $item),
              'quantity' => CRM_Utils_Array::value('quantity', $item),
            ];
            if ($this->_action == CRM_Core_Action::UPDATE) {
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

        if ($this->_action == CRM_Core_Action::UPDATE) { //remove  adhoc charges that have been removed
          $adhocChargesToBeRemoved = [];
          foreach ($currentAdhocCharges as $key => $adc) {
            if (!in_array($key, $newAdhocChargesIds)) {
              $adhocChargesToBeRemoved[$key] = $adc;
            }
          }
          if (!empty($adhocChargesToBeRemoved)) {
            foreach ($adhocChargesToBeRemoved as $key => $adc) {
              civicrm_api3('AdhocCharges', 'delete', ['id' => $key]);
            }
          }
        }
      }

      //End process
      parent::postProcess();

      $cid = CRM_Utils_Array::value('primary_contact_id', $bookingInfo);
      // user context
      $url = CRM_Utils_System::url('civicrm/contact/view/booking',
        "reset=1&id=$bookingID&cid=$cid&action=view"
      );
      CRM_Core_Session::setStatus($booking['title'], E::ts('Saved'), 'success');
      CRM_Utils_System::redirect($url);
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

  public static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()

      ->addStyleFile(E::LONG_NAME, 'css/booking.css', 92, 'page-header')
      ->addScriptFile('civicrm', 'packages/backbone/json2.js', 100, 'html-header', FALSE)
      ->addScriptFile(E::LONG_NAME, 'packages/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)

      ->addScriptFile(E::LONG_NAME, 'js/booking/booking-info/app.js', 150, 'html-header')
      ->addScriptFile(E::LONG_NAME, 'js/booking/booking-info/view.js', 170, 'html-header');

    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath(E::LONG_NAME) . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Booking/tpl/booking-info/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add([
        'template' => $fileName,
      ]);
    }
  }

}
