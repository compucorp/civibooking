<?php
use CRM_Booking_ExtensionUtil as E; 

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
  //protected $_id;

  
  private $configOptions;
  
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
    
    $config = CRM_Core_Config::singleton();
    /**
     * [dateformatDatetime] => %B %E%f, %Y %l:%M %P
     * [dateformatFull] => %B %E%f, %Y
     * [dateformatPartial] => %B %Y
     * [dateformatYear] => %Y
     * [dateformatTime] => %l:%M %P
     */
    $this->crmDateFormat = $config->dateformatDatetime; //retrieve crmDateFormat
    $this->assign('dateFormat', $this->crmDateFormat);

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
    $config = CRM_Booking_BAO_BookingConfig::getConfig();
    $this->assign('colour', CRM_Utils_Array::value('slot_new_colour', $config));

    list($xStart, $xSize, $xStep) = CRM_Booking_Utils_DateTime::getCalendarTime();
    $this->assign('xStart', $xStart);
    $this->assign('xSize', $xSize);
    $this->assign('xStep', $xStep);
    
    CRM_Core_Resources::singleton()->addVars('booking', 
        array('edit_mode' => $this->_action & CRM_Core_Action::UPDATE ? 1 : 0));

    $this->assign('timeOptions', CRM_Booking_Utils_DateTime::getTimeRange());
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      $title = CRM_Core_DAO::getFieldValue('CRM_Booking_BAO_Booking', $this->_id, 'title', 'id');
      CRM_Utils_System::setTitle(E::ts('Edit Booking') . " - $title");
    }else{
      CRM_Utils_System::setTitle(E::ts('New Booking') );
    }
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
      $params =   array(
        'id' => $this->_id,
      );
      
      CRM_Booking_BAO_Booking::retrieve($params, $booking);
      $result = civicrm_api3('Slot', 'get', array('booking_id' => $this->_id, 'is_deleted' => 0));
      $config = CRM_Booking_BAO_BookingConfig::getConfig();
      $slots = array();
      foreach ($result['values'] as $key => $value) {
        $displayName = CRM_Contact_BAO_Contact::displayName(CRM_Utils_Array::value('primary_contact_id', $booking));
        $configOptItem = $this->getConfigOptionById(CRM_Utils_Array::value('config_id', $value));
        //manipulate quantity to display in basket with "quantity" x "configuration (with price)", ie, "3 x People (30) = 90"
        $displayQuantity = CRM_Utils_Array::value('quantity', $value)
          .' x '.CRM_Utils_Array::value('unit_id',$configOptItem)
          .' ('.CRM_Utils_Array::value('price',$configOptItem).')';
        
        $slots[$key] = array(
          'id' => CRM_Utils_Array::value('id', $value),
          'resource_id' => CRM_Utils_Array::value('resource_id', $value),
          
          'start_date' => CRM_Utils_Array::value('start', $value) ,
          'end_date' => CRM_Utils_Array::value('end', $value) ,
          
          'label' => CRM_Core_DAO::getFieldValue(
            'CRM_Booking_BAO_Resource',
            CRM_Utils_Array::value('resource_id', $value),
            'label',
            'id'
          ),
          'text' =>  CRM_Utils_Array::value('booking_id', $value) . ' : ' . $displayName,
          'configuration_id' => CRM_Utils_Array::value('config_id', $value),
          'quantity' => CRM_Utils_Array::value('quantity', $value),
          'quantity_display' => $displayQuantity,
          //price = quantity * resource config price
          'price' => CRM_Utils_Array::value('quantity', $value) * floatval(CRM_Core_DAO::getFieldValue(
            'CRM_Booking_BAO_ResourceConfigOption',
            CRM_Utils_Array::value('config_id', $value),
            'price',
            'id'
          )),
          'note' => CRM_Utils_Array::value('note', $value),
          'color' =>  CRM_Utils_Array::value('slot_being_edited_colour', $config),
          'is_updated' => TRUE,
          'booking_id' => CRM_Utils_Array::value('booking_id', $value),
        );
      }
      $firstSlot = reset($slots);
      if($firstSlot){
        $slotStartDate = $firstSlot['start_date'];
        $this->assign('bookingSlotDate', $slotStartDate);
      }
      $this->assign('bookingId', $this->_id);
      
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
               E::ts('Resource(s)'),
               FALSE);

    $buttons = array(
      array(
        'type' => 'next',
        'name' => E::ts('Next >>'),
        'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        'isDefault' => TRUE,
      ),
    );

    $this->addButtons($buttons);

  }

  public function postProcess() {}

  /**
   * Display Name of the form
   *
   * @access public
   *
   * @return string
   */
  public function getTitle() {
    return E::ts('Select resources');
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

      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'packages/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/moment.min.js', 120, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)

      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/add-sub-resource/app.js', 140, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/utils.js', 141, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/civicrm-moment-strftime.js', 142, 'html-header', FALSE)

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

  private function getConfigOptionById($configurationId){
    if(!$this->configOptions){
      //load configuration option
      $params = array('sequential' => 1,);
      $configOptionResult = civicrm_api3('ResourceConfigOption', 'get', $params);
      $this->configOptions = CRM_Utils_Array::value('values', $configOptionResult);
    }
    $configOptItems = $this->configOptions;
    foreach ($configOptItems as $key => $value) {
      if(CRM_Utils_Array::value('id', $value) == $configurationId){
        return $configOptItems[$key];
      }
    }
    return null;
  }
}
