<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_AddSubResource extends CRM_Core_Form {

  function preProcess(){

  }

   /**
   * This function sets the default values for the form.
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    //$val = $this->controller->exportValues('CheckResource');
    //dprint_r(json_decode($val['resources']));
  }

  function buildQuickForm() {
    parent::buildQuickForm();

    $this->add('textarea',
              'resources',
               ts('Resource(s)'),
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
      //->addStyleFile('uk.co.compucorp.civicrm.Booking', 'css/bootstrap-modal.css', 90, 'page-header')
      //->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/schedule.css', 91, 'page-header')
      //->addStyleFile('uk.co.compucorp.civicrm.booking', 'js/vendor/dhtmlxScheduler/sources/dhtmlxscheduler.css', 92, 'page-header')
      //->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/booking.css', 92, 'page-header')


      ->addScriptFile('civicrm', 'packages/backbone/json2.js', 100, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/underscore.js', 110, 'html-header', FALSE)

      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)


      ->addScriptFile('uk.co.compucorp.civicrm.booking', 'js/vendor/moment.min.js', 120, 'html-header', FALSE)

      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/vendor/bootstrap-modal.js', 131, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/booking-process/add-sub-resource/app.js', 150, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/booking-process/add-sub-resource/router.js', 152, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/booking-process/add-sub-resource/view.js', 160, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/booking-process/add-sub-resource/model.js', 164, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.Booking', 'js/booking-process/add-sub-resource/collection.js', 165, 'html-header');


    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath('uk.co.compucorp.civicrm.booking') . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Booking/tpl/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add(array(
        'template' => $fileName,
      ));
    }
  }


}
