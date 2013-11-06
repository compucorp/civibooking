<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_DayView extends CRM_Core_Form {
    
  function buildQuickForm() {
    // add form elements
    $this->addDate('dayview_select_date', ts('Select Booking Date'), TRUE, array('formatType' => 'activityDate' ));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'reset',
        'name' => ts('Reset'),
      ),
    ));
    // export form elements
    parent::buildQuickForm();
  }

  function preProcess() {
       self::registerScripts();
  }
  
  function postProcess() {
    $values = $this->exportValues();
    
    $selectedDate = CRM_Utils_Array::value('dayview_select_date',$values);
    
    //get booking slots from selected date
    $from = CRM_Utils_Date::processDate($selectedDate);
    $resources = CRM_Booking_BAO_Slot::getSlotDetailsOrderByResourceBetweenDate($from, $from);
    //put resources result to values, being ready to display.
    $values['resources'] = $resources;
    
    if(empty($resources)){  //check empty result
        //Convert date to show on no match found view
        $values['dayview_select_date'] = DateTime::createFromFormat('m/d/Y',$selectedDate)->format('d/m/Y');
    }else{
        //Convert date to compile with crmDate 
        $values['dayview_select_date'] = DateTime::createFromFormat('m/d/Y',$selectedDate)->format('Y-m-d');
    }

    //assign values to show on template    
    $this->assign($values);
    //parent::postProcess();
  }

    static function registerScripts() {
        static $loaded = FALSE;
        if ($loaded) {
          return;
        }
        $loaded = TRUE;
        CRM_Core_Resources::singleton()
              ->addScriptFile('uk.co.compucorp.civicrm.booking', 'CRM/Booking/Form/DayView.js', 10, 'html-header', FALSE);
    
    }
}
