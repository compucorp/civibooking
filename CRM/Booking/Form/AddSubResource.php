<?php

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
    protected $_bookingId;


    /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return ts('Add sub resources');
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

    $selectResourcePage = $this->controller->exportValues('SelectResource');
    $selectedResources = json_decode($selectResourcePage['resources'], true);
    $this->assign('resources', $selectedResources);

    foreach ($selectedResources as $key => $resource) {
      $this->_subTotal += $resource['price'];
    }
    $this->_total = $this->_subTotal;

    require_once 'CRM/Booking/Utils/DateTime.php';
    $this->assign('timeOptions', CRM_Booking_Utils_DateTime::getTimeRange());

    // get all custom groups sorted by weight
    $items = array();
    $bao = new CRM_Booking_BAO_AdhocChargesItem();
    $bao->orderBy('weight');
    $bao->is_active = 1;
    $bao->find();
    while ($bao->fetch()) {
      $items[$bao->id] = array();
      CRM_Core_DAO::storeValues($bao, $items[$bao->id]);
    }

    $days = CRM_Booking_Utils_DateTime::getDays();
    $months = CRM_Utils_Date::getFullMonthNames();
    $years = CRM_Booking_Utils_DateTime::getYears();

    $this->assign('days', $days);
    $this->assign('months', $months);
    $this->assign('years', $years);

    $this->assign('items', $items);
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
    $defaults['sub_total'] = $this->_subTotal;
    $defaults['adhoc_charge'] = 0;
    $defaults['discount_amount']=0;
    $defaults['total_price'] = $this->_total;

    //{"sub_resources":{"1380046945":{"parent_ref_id":408,"ref_id":1380046945,"resource":{"id":"4","label":"Tea and coffee"},"configuration":{"id":"1","label":"Full tea and coffee set - $300.00 / Head","price":"300.00"},"quantity":"20","time_required":"8:00","note":"","price_estimate":6000}},"resources":{"408":6300},"sub_total":6300,"adhoc_charges":{"total":0},"total_price":6300}

    return $defaults;
  }

  function buildQuickForm() {
    parent::buildQuickForm();

    $this->addElement('text',
                      'sub_total',
                      ts('Sub total'));

    $this->addElement('text',
                      'total_price',
                      ts('Total'));

    $this->addElement('text',
                      'discount_amount',
                      ts('Discount amount'));

    $this->addElement('text',
                      'adhoc_charge',
                      ts('Ad-hoc charges'));

    $this->add('textarea',
              'sub_resources',
               ts('Sub Resource(s)'),
               FALSE);

    $buttons = array(
      array('type' => 'back',
        'name' => ts('<< Previous'),
      ),
      array(
        'type' => 'next',
        'name' => ts('Next >>'),
        'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        'isDefault' => TRUE,
      ),
    );

    $this->addButtons($buttons);

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
      ->addScriptFile('civicrm', 'packages/backbone/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/moment.min.js', 120, 'html-header', FALSE)

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
