<?php
use CRM_Booking_ExtensionUtil as E; 

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_AddSubResource extends CRM_Core_Form {

    protected $_subTotal;
    protected $_total;
    protected $_discountAmount;
    protected $_resourcesPrice;

    /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return E::ts('Add unlimited resources');
  }

  function preProcess(){

    $this->_id = $this->get('id');

    $config = CRM_Core_Config::singleton();
    $currencySymbols = "";
    if(!empty($config->currencySymbols)){
      $currencySymbols = $config->currencySymbols;
    }else{
      $currencySymbols = $config->defaultCurrencySymbol;
    }
    $this->assign('currencySymbols', $currencySymbols);
    
    //Control the flexibility of time configuration for unlimited resource
    $bookingConfig = CRM_Booking_BAO_BookingConfig::getConfig();
    $this->assign('timeconfig', CRM_Utils_Array::value('unlimited_resource_time_config', $bookingConfig));

    $selectResourcePage = $this->controller->exportValues('SelectResource');

    $selectedResources = json_decode($selectResourcePage['resources'], true);
    $this->assign('resources', $selectedResources);

    foreach ($selectedResources as $key => $resource) {
      $this->_subTotal += $resource['price'];
      $this->_resourcesPrice[$key] = $resource['price'];
      $this->_discountAmount = 0;
    }
    $this->_total = $this->_subTotal;

    require_once 'CRM/Booking/Utils/DateTime.php';
    $this->assign('timeOptions', CRM_Booking_Utils_DateTime::getTimeRange());

    // get all custom groups sorted by weight
    $items = array();
    $bao = new CRM_Booking_BAO_AdhocChargesItem();
    $bao->orderBy('weight');
    $bao->is_active = 1;
    $bao->is_deleted = 0;
    $bao->find();
    while ($bao->fetch()) {
      $items[$bao->id] = array();
      CRM_Core_DAO::storeValues($bao, $items[$bao->id]);
      $items[$bao->id]['name'] = preg_replace('/[^\p{L}\p{N}\s]/u', '_', $items[$bao->id]['name']);
    }

    //$days = CRM_Booking_Utils_DateTime::getDays();
    //$months = CRM_Utils_Date::getFullMonthNames();
    //$years = CRM_Booking_Utils_DateTime::getYears();


    $this->assign('items', $items);
    
    $baoUnlimResource = new CRM_Booking_BAO_Resource();
    $baoUnlimResource->is_active = 1;
    $baoUnlimResource->is_deleted = 0;
    $baoUnlimResource->is_unlimited = 1;
    $unlimitedResourceCount = $baoUnlimResource->count();
    
    $this->assign('unlimited_resource_count', $unlimitedResourceCount);
    
    if($this->_id && $this->_action == CRM_Core_Action::UPDATE){
      $title = CRM_Core_DAO::getFieldValue('CRM_Booking_BAO_Booking', $this->_id, 'title', 'id');
      CRM_Utils_System::setTitle(E::ts('Edit Booking') . " - $title");
    }else{
      CRM_Utils_System::setTitle(E::ts('New Booking') );
    }

    /**
     * [dateformatDatetime] => %B %E%f, %Y %l:%M %P
     * [dateformatFull] => %B %E%f, %Y
     * [dateformatPartial] => %B %Y
     * [dateformatYear] => %Y
     * [dateformatTime] => %l:%M %P
     */
    $this->crmDateFormat = $config->dateformatDatetime; //retrieve crmDateFormat
    $this->assign('dateFormat', $this->crmDateFormat);

    self::registerScripts();

  }

   /**
   * This function sets the default values for the form.
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    $defaults = array( );
    if($this->_id){
      $result = civicrm_api3('Booking', 'get', array('id' => $this->_id));
      $booking = $result['values'][$result['id']];
      $subResources['sub_resources'] = array();
      $subResources['resources'] = $this->_resourcesPrice;
      $slots = civicrm_api3('Slot', 'get', array('booking_id' => $this->_id, 'is_deleted' => 0));
      $unitPriceList =  CRM_Booking_BAO_ResourceConfigOption::buildOptions('unit_id', 'create');
      foreach ($slots['values'] as $key => $slot) {
        $subSlots = civicrm_api3('SubSlot', 'get', array('slot_id' => $slot['id'], 'is_deleted' => 0));
        foreach ($subSlots['values'] as $subSlot) {
          $subResources['sub_resources'][$subSlot['id']] = array(
            "parent_ref_id" => $slot['id'],
            "ref_id" => $subSlot['id'],
            "quantity" => CRM_Utils_Array::value('quantity', $subSlot),
            "time_required" => "2013-10-21 09:50",
            "time_required" =>  CRM_Utils_Array::value('time_required', $subSlot),
            "note" =>  CRM_Utils_Array::value('note', $subSlot),
          );
          $resourceResult = civicrm_api3('Resource', 'get', array('id' => $subSlot['resource_id']));
          $resource = $resourceResult['values'][$subSlot['resource_id']];
          $subResources['sub_resources'][$subSlot['id']]['resource'] = array(
            "id" => $resource['id'],
            "label" => $resource['label']
          );
          $configOptionResult = civicrm_api3('ResourceConfigOption', 'get', array('id' => $subSlot['config_id']));
          $configOption = $configOptionResult['values'][$subSlot['config_id']];
          $unit = $unitPriceList[$configOption['unit_id']];
          $subResources['sub_resources'][$subSlot['id']]['configuration'] = array(
            "id" => $configOption['id'],
            "label" => $configOption['label'] . ' - ' . $configOption['price'] . ' / ' . $unit,
            "price" => $configOption['price'],
          );
          $priceEstimate =  $configOption['price'] *  CRM_Utils_Array::value('quantity', $subSlot);
          $subResources['sub_resources'][$subSlot['id']]['price_estimate'] =  $priceEstimate;
          $resourceTotalPrice =  $subResources['resources'][$slot['id']] + $priceEstimate;
          $subResources['resources'][$slot['id']] = $resourceTotalPrice;
        }
      }
      $subTotal = 0;
      foreach ($subResources['resources'] as $price) {
        $subTotal += $price;
      }
      $addhocCharges = array("items" => array(), "note" => CRM_Utils_Array::value('adhoc_charges_note', $booking), "total" => 0);
      $addhocChargesResult = civicrm_api3('AdhocCharges', 'get', array('booking_id' => $this->_id, 'is_deleted' => 0));
      foreach ($addhocChargesResult['values'] as $key => $charge) {
        $itemResult = civicrm_api3('AdhocChargesItem', 'get', array('id' => $charge['item_id'], 'is_deleted' => 0));
        if(empty($itemResult['values'])){ //make sure we do not process deleted item
          continue;
        }
        $item = $itemResult['values'][$charge['item_id']];
        $totalPrice = $item['price'] * $charge['quantity'];
        $addhocCharges['items'][$charge['item_id']] = array(
          "item_id" => $charge['item_id'],
          "name" => $item['name'],
          "price" => $totalPrice,
          "quantity" => $charge['quantity'],
          "item_price" => $item['price']
        );
        $addhocCharges['total'] +=  $totalPrice;
      }
      
      
      $defaults['sub_total'] = $subTotal;
      $defaults['adhoc_charge'] = $addhocCharges['total'];
      $defaults['discount_amount']= CRM_Utils_Array::value('discount_amount', $booking);
      $defaults['discount_amount_dummy']= CRM_Utils_Array::value('discount_amount', $booking);
      
      $subResources['sub_total'] = $subTotal;
      $subResources['adhoc_charges'] = $addhocCharges;
      $total = ($subTotal - $defaults['discount_amount']) +  $addhocCharges['total'];
      $subResources['total_price'] = $total;
      // force JSON to encode empty array as object if there is empty array in $subResources
      $defaults['sub_resources'] =  json_encode($subResources,JSON_FORCE_OBJECT);
      $defaults['total_price'] = $total;
    }else{
      $defaults['sub_total'] = $this->_subTotal;
      $defaults['adhoc_charge'] = 0;
      $defaults['discount_amount']= 0;
      $defaults['total_price'] = $this->_total;
    }
    return $defaults;
  }

  function buildQuickForm() {
    parent::buildQuickForm();

    $this->addElement('text',
                      'sub_total',
                      E::ts('Sub total'));

    $this->addElement('text',
                      'total_price',
                      E::ts('Total'));

    $this->addElement('text',
                      'discount_amount',
                      E::ts('Discount amount'));

    //for discount amount calculation
    $this->addElement('text',
                      'discount_amount_dummy',
                      E::ts('Discount amount'));

    $this->addElement('text',
                      'adhoc_charge',
                      E::ts('Ad-hoc charges'));

    $this->add('textarea',
              'sub_resources',
               E::ts('Unlimited Resource(s)'),
               FALSE);

    $buttons = array(
      array('type' => 'back',
        'name' => E::ts('<< Previous'),
      ),
      array(
        'type' => 'next',
        'name' => E::ts('Next >>'),
        'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        'isDefault' => TRUE,
      ),
    );

    $this->addButtons($buttons);
    
    $this->addRule("discount_amount_dummy", E::ts('Please enter a valid amount.'), 'money');
    
    $this->addFormRule( array( 'CRM_Booking_Form_AddSubResource', 'formRule' ), $this );

  }
  
  //set the validation rule for the discount field
  static function formRule($params, $files, $context)
  {
    $values = $context->exportValues();
    $numValidate = is_numeric($values['discount_amount']);
    $emptyValidate = $values['discount_amount']=='';
    $finalSubtotal = $values['sub_total'] + $values['adhoc_charge'];
    $rangeValidate = $values['discount_amount']>=0 && $values['discount_amount']<=$finalSubtotal;
    if(!$numValidate && !$emptyValidate){
      return array('discount_amount' => 'Please enter a valid number.');
    }elseif(!$rangeValidate){
      return array('discount_amount' => 'Please enter a number in the valid range.');
    }else{
      return TRUE;
    }
  }

  function postProcess() {
    $values = $this->exportValues();

    parent::postProcess();
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
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'packages/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/moment.min.js', 120, 'html-header', FALSE)

      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/civicrm-moment-strftime.js', 140, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/add-sub-resource/app.js', 150, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/common/views.js', 151, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/utils.js', 151, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/add-sub-resource/entities.js', 160, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/booking/add-sub-resource/view.js', 170, 'html-header');


    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath('uk.co.compucorp.civicrm.booking') . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Booking/tpl/add-sub-resource/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add(array(
        'template' => $fileName,
      ));
    }
    $region->add(array('template' => 'CRM/Booking/tpl/select-option.tpl' ));

  }
}
