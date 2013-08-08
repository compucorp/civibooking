<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Civibooking_Form_AddResource extends CRM_Core_Form {
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

    $values = CRM_Core_OptionGroup::valuesByID(97);
    $this->assign('resourceTypes', $values);

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
      // add checkboxes for resource type
    $resources = array();

    $this->add('select', 'resources', ts('Resource(s)'), $resources, FALSE,
              array(
                'id' => 'resources', 
                'multiple' => 'multiple',)
    );

    $buttons = array(
      array(
        'type' => 'next',
        'name' => ts('Update basket >>'),
        'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        'isDefault' => TRUE,
      ),    
    );

    $this->addButtons($buttons);

  }

  public function postProcess() {
    $params = $ids = array();

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
      ->addScriptFile('civicrm', 'packages/backbone/json2.js', 100, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/underscore.js', 110, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.js', 120, 'html-header')
      ->addScriptFile('civicrm', 'packages/backbone/backbone.marionette.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'packages/backbone/backbone.modelbinder.js', 125, 'html-header', FALSE)
      ->addScriptFile('civicrm', 'js/crm.backbone.js', 130, 'html-header', FALSE)
      ->addStyleFile('uk.co.compucorp.civicrm.civibooking', 'css/schedule.css', 140, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.civibooking', 'js/resource-search/app.js', 150, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.civibooking', 'js/resource-search/layout.js', 151, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.civibooking', 'js/resource-search/router.js', 152, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.civibooking', 'js/resource-search/view.js', 160, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.civibooking', 'js/resource-search/model.js', 164, 'html-header')
      ->addScriptFile('uk.co.compucorp.civicrm.civibooking', 'js/resource-search/collection.js', 165, 'html-header');

   

    $templateDir = CRM_Extension_System::singleton()->getMapper()->keyToBasePath('uk.co.compucorp.civicrm.civibooking') . '/templates/';
    $region = CRM_Core_Region::instance('page-header');
    foreach (glob($templateDir . 'CRM/Civibooking/tpl/*.tpl') as $file) {
      $fileName = substr($file, strlen($templateDir));
      $region->add(array(
        'template' => $fileName,
      ));
    }
  }

}
