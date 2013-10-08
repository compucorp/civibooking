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
    $config = CRM_Core_Config::singleton();
    $currencySymbols = "";
    if(!empty($config->currencySymbols)){
      $currencySymbols = $config->currencySymbols;
    }else{
      $currencySymbols = $config->defaultCurrencySymbol;
    }
    $this->assign('currencySymbols', $currencySymbols);

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

    $this->add('text', 'title', ts('Title'), array(), TRUE);
    $this->addDate('event_start_date', ts('Date'), TRUE, array('formatType' => 'activityDate'));
    $this->add('textarea', 'description', ts('Description'));
    $this->add('textarea', 'note', ts('Note'));

    $this->add('text', 'enp', ts('Estimate number of participants'));
    $this->add('text', 'fnp', ts('Final number of participants'));


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
      $errors['primary_contact_id'] = ts('This field is required.');
    }
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    $addSubResourcePage = $this->controller->exportValues('AddSubResource');
    $defaults['total_amount'] = $addSubResourcePage['total_price']; //use the amount that passing from the form
    return $defaults;
  }

  function postProcess() {
    $bookingInfo = $this->exportValues();
    $selectResource = $this->controller->exportValues('SelectResource');
    $addSubResoruce = $this->controller->exportValues('AddSubResource');
    $resourcesValue = json_decode($selectResource['resources'], true);
    $subResourcesValue = json_decode($addSubResoruce['sub_resources'], true);
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
    $booking['resources'] = $resources;
    $booking['adhoc_charges'] = $adhocCharges;

    $booking['primary_contact_id'] = CRM_Utils_Array::value('primary_contact_select_id', $bookingInfo);
    $booking['secondary_contact_id'] = CRM_Utils_Array::value('secondary_contact_select_id', $bookingInfo);
    $booking['po_number'] = CRM_Utils_Array::value('po_no', $bookingInfo);
    $booking['status_id'] = CRM_Utils_Array::value('booking_status', $bookingInfo);
    $booking['title'] =CRM_Utils_Array::value('title', $bookingInfo);


    $booking['description'] =CRM_Utils_Array::value('description', $bookingInfo);
    $booking['notes'] = CRM_Utils_Array::value('note', $bookingInfo);
    $booking['event_date'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('event_start_date', $bookingInfo));

    $booking['discount_amount'] = CRM_Utils_Array::value('discount_amount', $addSubResoruce);
    $booking['total_amount'] = CRM_Utils_Array::value('total_price', $addSubResoruce);

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

    $booking['validate'] = FALSE; //Make sure we ignore slot validation
    try{
      $result = civicrm_api3('Booking', 'Create', $booking);
      $bookingID = CRM_Utils_Array::value('id', $result);
      $booking =  CRM_Utils_Array::value($bookingID, CRM_Utils_Array::value('values', $result));
      $this->_id = $bookingID;
      $this->_values = $booking;
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
