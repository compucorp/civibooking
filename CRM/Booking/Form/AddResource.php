<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_AddResource extends CRM_Core_Form {
 /**
   * the booking ID of the booking if we are editing a booking
   *
   * @var integer
   */
  protected $_bookingID;

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {

    $config = CRM_Core_Config::singleton();
    $currencySymbols = "";
    if(!empty($config->currencySymbols)){
      $currencySymbols = $config->currencySymbols;
    }else{
      $currencySymbols = $config->defaultCurrencySymbol;
    }

    $resourceTypes = CRM_Booking_BAO_Resource::getResourceTypes(false);
    foreach ($resourceTypes as $key => $type) {
      $result = CRM_Booking_BAO_Resource::getResourcesByType($key);
      $rTypekey = trim(strtolower($key . '_' . $type['label']));
      $resources[$rTypekey]['label'] = $type['label'];
      $resources[$rTypekey]['child'] = $result;
    }

    $this->assign('resources', $resources);;
    $this->assign('currencySymbols', $currencySymbols);

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
    dprint_r($this->_action);

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
    return ts('Add resources');
  }

  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()
     // ->addStyleFile('uk.co.compucorp.civicrm.Booking', 'css/bootstrap-modal.css', 90, 'page-header')
      ->addStyleFile('uk.co.compucorp.civicrm.Booking', 'css/schedule.css', 91, 'page-header')
      ->addStyleFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/dhtmlxscheduler.css', 92, 'page-header')
      ->addStyleFile('uk.co.compucorp.civicrm.Booking', 'css/Booking.css', 92, 'page-header')


      /* ->addScriptFile('civicrm', 'packages/backbone/json2.js', 100, 'html-header', FALSE) */
      ->addScriptFile('civicrm', 'packages/backbone/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/moment.min.js', 120, 'html-header', FALSE)

      /*->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE) */
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/dhtmlxscheduler.js', 132, 'html-header')
      //->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_mvc.js', 133, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_timeline.js', 134, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_treetimeline.js', 135, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_minical.js', 136, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_readonly.js', 137, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/dhtmlxScheduler/sources/ext/dhtmlxscheduler_collision.js', 138, 'html-header');


      /*
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/bootstrap-modal.js', 131, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/resource-search/app.js', 150, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/resource-search/router.js', 152, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/resource-search/view.js', 160, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/resource-search/model.js', 164, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/resource-search/collection.js', 165, 'html-header'); */


    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath('uk.co.compucorp.civicrm.Booking') . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Booking/tpl/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add(array(
        'template' => $fileName,
      ));
    }
  }

}
