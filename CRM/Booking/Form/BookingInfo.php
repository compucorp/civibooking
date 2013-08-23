<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_BookingInfo extends CRM_Core_Form {

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

    $this->addElement('hidden', "contact_select_id");
    $this->add('text', "contact", ts('Select contact'));

    $this->addElement('hidden', "organisation_select_id");
    $this->add('text', "organisation", ts('Organisation'));

    $this->add('text', 'po_no', ts('Purchase order number'));


    $bookingStatus= CRM_Booking_BAO_Resource::buildOptions('status_id', 'create');
    $this->add('select', 'booking_status', ts('Booking status'),
        $bookingStatus, FALSE,
        array(
          'id' => 'booking_status',
          'title' => '- ' . ts('select') . ' -',
        )
    );



    $this->add('text', 'title', ts('title'));
    $this->addDateTime('event_start_date', ts('Date'), FALSE, array('formatType' => ''));
    $this->add('textarea', 'description', ts('Description'));
    $this->add('textarea', 'note', ts('Note'));

    $this->add('text', 'enp', ts('Estimate number of participants'));
    $this->add('text', 'fnp', ts('Final number of participants'));

    $this->addElement('checkbox',
      'send_conformation',
      ts('Send booking comformation email?'), NULL,
      array('onclick' => "showHideByValue( 'send_receipt', '', 'notice', 'table-row', 'radio', false); showHideByValue( 'send_receipt', '', 'fromEmail', 'table-row', 'radio', false);")
    );

    $emailToContacts = array();
    $this->add('select', 'email_to', ts('Email to'),
      $emailToContacts, FALSE,
      array(
        'id' => 'email_to',
        'title' => '- ' . ts('select') . ' -',
      )
    );


    $this->addElement('checkbox', 'record_contribution', ts('Record Booking Payment?'));

      // subtype is a common field. lets keep it here
    $paymentContacts = array();
    $this->add('select', 'select_payment_contact', ts('Select contact'),
      $paymentContacts, FALSE,
      array(
        'id' => 'select_payment_contact',
        'title' => '- ' . ts('select') . ' -',
      )
    );


    $this->addDate('receive_date', ts('Received'), FALSE, array('formatType' => 'activityDateTime'));


    $this->assign('amount', 300);

    $this->add('select', 'financial_type_id',
      ts('Financial Type'),
      array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::financialType()
    );

    $this->add('select', 'payment_instrument_id',
        ts('Paid By'),
        array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::paymentInstrument(),
        FALSE,
        array()
    );

    $this->add('text', 'trxn_id', ts('Transaction ID'));

    $this->add('select', 'contribution_status_id',
        ts('Payment Status'),
        array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::contributionStatus(),
        FALSE,
        array()
    );

    $this->addElement('checkbox',
      'send_receipt',
      ts('Send Confirmation and Receipt?'), NULL,
      array('onclick' => "showHideByValue( 'send_receipt', '', 'notice', 'table-row', 'radio', false); showHideByValue( 'send_receipt', '', 'fromEmail', 'table-row', 'radio', false);")
    );

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

  }

  function postProcess() {
    $values = $this->exportValues();

    dprint_r($this);
    exit;

    parent::postProcess();
  }

  static function registerScripts() {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    CRM_Core_Resources::singleton()

      ->addStyleFile('uk.co.compucorp.civicrm.booking', 'css/booking.css', 92, 'page-header');




  }


}
