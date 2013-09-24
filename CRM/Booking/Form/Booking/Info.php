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
        'name' => ts('Complate and save'),
      ),
    );

    $this->addButtons($buttons);

    $this->addFormRule( array( 'CRM_Booking_Form_Booking_Info', 'formRule' ), $this );


  }

  static function formRule($params, $files, $self) {
    $errors = parent::rules($params, $files, $self);
    //make sure primary contact is selected
    $contactId = CRM_Utils_Array::value('primary_contact_select_id', $params);
    if(!$contactId){
      $errors['primary_contact_id'] = ts('This field is required.');
    }
    $secondaryContactId = CRM_Utils_Array::value('secondary_contact_select_id', $params);
    return empty($errors) ? TRUE : $errors;
  }


  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    $addSubResourcePage = $this->controller->exportValues('AddSubResource');
    $defaults['total_amount'] = $addSubResourcePage['total_price'];
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
      $resource['start_date'] = CRM_Utils_Date::processDate($resource['start_date']);
      $resource['start_date'] = CRM_Utils_Date::processDate($resource['end_date']);
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

    $recordContribution = CRM_Utils_Array::value('record_contribution', $bookingInfo);
    if(!$recordContribution){
       //FIXED ME: Get default contribution status or pending from option value
      //Note hard code
      $booking['payment_status_id'] = 2;

    }else{

      $booking['payment_status_id'] = CRM_Utils_Array::value('contribution_status_id', $bookingInfo);

    }

    $now  = date('YmdHis');
    $session =& CRM_Core_Session::singleton( );
    $booking['created_by'] =  $session->get( 'userID' );
    $booking['created_date'] = $now;
    $booking['updated_by'] = $session->get( 'userID' );
    $booking['updated_date'] = $now;

    $booking['version'] = 3; //Add version 3 before calling APIs
    $booking['validate'] = FALSE; //Make sure we ignore slot validation

    $bookingResult = civicrm_api('Booking', 'Create', $booking);

    if(CRM_Utils_Array::value('id', $bookingResult)){
      if($recordContribution){
        $values = array();
        if(CRM_Utils_Array::value('select_payment_contact', $bookingInfo) == 1){
          $values['payment_contact'] =  CRM_Utils_Array::value('primary_contact_select_id', $bookingInfo);
        }else{
          $values['payment_contact'] =  CRM_Utils_Array::value('secondary_contact_select_id', $bookingInfo);
        }

        $values['total_amount'] = round(CRM_Utils_Array::value('total_price', $addSubResoruce), 2);
        $values['booking_id'] = CRM_Utils_Array::value('id', $bookingResult);
        $values['receive_date'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('receive_date', $bookingInfo));
        $values['financial_type_id'] = CRM_Utils_Array::value('financial_type_id', $bookingInfo);
        $values['payment_instrument_id'] = CRM_Utils_Array::value('payment_instrument_id', $bookingInfo);
        $values['trxn_id'] = CRM_Utils_Array::value('trxn_id', $bookingInfo);
        //Payment status is a contribution status
        $values['payment_status_id'] = CRM_Utils_Array::value('contribution_status_id', $bookingInfo);
        $values['booking_title'] = CRM_Utils_Array::value('title', $bookingInfo);

        CRM_Booking_BAO_Booking::recordContribution($values);
      }

      $sendConfirmation = CRM_Utils_Array::value('send_conformation', $bookingInfo);
      if($sendConfirmation){
        //$session =& CRM_Core_Session::singleton( );
        //$values['source_contact_id'] =$session->get( 'userID' ); // which is contact id of the user
        $fromEmailAddress = CRM_Core_OptionGroup::values('from_email_address');
        $values['from_email_address'] = CRM_Utils_Array::value(CRM_Utils_Array::value('from_email_address', $bookingInfo), $fromEmailAddress);
        $values['booking_id'] = CRM_Utils_Array::value('id', $bookingResult);
        $emailTo = CRM_Utils_Array::value('email_to', $bookingInfo);
        $contactIds = array();
        if($emailTo == 1){
          array_push($contactIds, CRM_Utils_Array::value('primary_contact_select_id', $bookingInfo));
        }elseif ($emailTo == 2){
          array_push($contactIds, CRM_Utils_Array::value('secondary_contact_select_id', $bookingInfo));
        }else{
          array_push($contactIds, CRM_Utils_Array::value('primary_contact_select_id', $bookingInfo));
          array_push($contactIds, CRM_Utils_Array::value('secondary_contact_select_id', $bookingInfo));
        }
        foreach ($contactIds as $key => $cid) {
          $resturn = CRM_Booking_BAO_Booking::sendMail($cid, $values);
        }
        //Finally add booking activity
        CRM_Booking_BAO_Booking::createActivity(CRM_Utils_Array::value('id', $bookingResult));
      }
    }

    // user context
    $url = CRM_Utils_System::url('civicrm/booking/add',
      "reset=1"
    );

    CRM_Core_Session::setStatus($booking['title'], ts('Saved'), 'success');

    parent::postProcess();
    CRM_Utils_System::redirect( $url);

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
