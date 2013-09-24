<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_SelectResource extends CRM_Core_Form {
 /**
   * the booking ID of the booking if we are editing a booking
   *
   * @var integer
   */
  protected $_id;

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {

    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive',
      $this, FALSE, 0
    );
    $this->assign('bookingId', $this->_id);

    $days = CRM_Booking_Utils_DateTime::getDays();
    $months = CRM_Utils_Date::getFullMonthNames();
    $years = CRM_Booking_Utils_DateTime::getYears();

    $this->assign('days', $days);
    $this->assign('months', $months);
    $this->assign('years', $years);

    $config = CRM_Core_Config::singleton();
    $currencySymbols = "";
    if(!empty($config->currencySymbols)){
      $currencySymbols = $config->currencySymbols;
    }else{
      $currencySymbols = $config->defaultCurrencySymbol;
    }

    $resourceTypes = CRM_Booking_BAO_Resource::getResourceTypes();
    $resources = array();
    foreach ($resourceTypes as $key => $type) {
      $result = CRM_Booking_BAO_Resource::getResourcesByType($key);
      $rTypekey = trim(strtolower($key . '_' . $type['label']));
      $resources[$rTypekey]['label'] = $type['label'];
      $resources[$rTypekey]['child'] = $result;
    }

    $this->assign('resources', $resources);;
    $this->assign('currencySymbols', $currencySymbols);

    $this->assign('timeOptions', CRM_Booking_Utils_DateTime::getTimeRange());

    self::registerScripts();


  }

  /**
   * This function sets the default values for the form.
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {

    $defaults = array();
    if($this->_id){
      $params = array(
        'version' => 3,
        'booking_id' => $this->_id,
      );
      $result = civicrm_api('Slot', 'get', $params);
      $config = CRM_Booking_BAO_BookingConfig::getConfig();
      $slots = array();
      foreach ($result['values'] as $key => $value) {
        CRM_Booking_BAO_Booking::retrieve($params, $booking );
        $displayName = CRM_Contact_BAO_Contact::displayName(CRM_Utils_Array::value('primary_contact_id', $booking));
        $slots[$key] = array(
          'id' => CRM_Utils_Array::value('id', $value),
          'resource_id' => CRM_Utils_Array::value('resource_id', $value),
          'start_date' =>CRM_Utils_Array::value('start', $value) ,
          'end_date' => CRM_Utils_Array::value('end', $value),
          'label' => CRM_Booking_BAO_Resource::getFieldValue('label', CRM_Utils_Array::value('resource_id', $value)), // resource label
          'text' =>  CRM_Utils_Array::value('booking_id', $value) . ' : ' . $displayName,
          'configuration_id' => CRM_Utils_Array::value('config_id', $value),
          'quantity' => CRM_Utils_Array::value('quantity', $value),
          'price' => CRM_Booking_BAO_ResourceConfigOption::getFieldValue('price', CRM_Utils_Array::value('config_id', $value)), // resource price,
          'note' => CRM_Utils_Array::value('note', $value),
          'color' =>  CRM_Utils_Array::value('slot_being_edited_colour', $config),
        );
      }
      $defaults['resources'] = json_encode($slots);
    }
    return $defaults;
  }

  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $this->add('textarea',
              'resources',
               ts('Resource(s)'),
               FALSE);

    $buttons = array(
      array(
        'type' => 'next',
        'name' => ts('Next >>'),
        'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        'isDefault' => TRUE,
      ),
    );

    $this->addButtons($buttons);

  }



  public function postProcess() {
    //dprint_r($this->_action);

    //$params = $ids = array();


    $params = $this->exportValues();
   // dprint_r($params);
    $resources = explode(PHP_EOL, $params['resources']);
    //dprint_r($resources);
    //exit;

    $session = CRM_Core_Session::singleton();
    $params['created_id'] = $session->get('userID');

  }

  /**
   * Display Name of the form
   *
   * @access public
   *
   * @return string
   */
  public function getTitle() {
    return ts('Select resources');
  }

  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()
      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/schedule.css', 91, 'page-header')
      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/dhtmlxscheduler.css', 92, 'page-header')
      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/booking.css', 92, 'page-header')

      ->addScriptFile('civicrm', 'packages/backbone/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/moment.min.js', 120, 'html-header', FALSE)

      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/dhtmlxscheduler.js', 132, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_timeline.js', 134, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_treetimeline.js', 135, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_minical.js', 136, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_readonly.js', 137, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_collision.js', 138, 'html-header');

    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath('uk.co.compucorp.civicrm.booking') . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Booking/tpl/select-resource/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add(array(
        'template' => $fileName,
      ));
    }
    $region->add(array('template' => 'CRM/Booking/tpl/select-option.tpl' ));

  }

}
