<?php
use CRM_Booking_ExtensionUtil as E; 
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 * This base class for Update/Cancel/Delete Booking
 *
 */
abstract class CRM_Booking_Form_Booking_Base extends CRM_Core_Form {

  protected $_id;

  protected $_cid;

  protected $_values;

  protected $_cancelStatusId;

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->_id = $this->get('id');
    $this->_cid = $this->get('cid');
    $params  = array('id' => $this->_id);

    CRM_Booking_BAO_Booking::retrieve($params, $this->_values );

    if (empty($this->_values)) {
      CRM_Core_Error::statusBounce(E::ts('The requested booking record does not exist (possibly the record was deleted).'));
    }

    $params = array(
      'option_group_name' => CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS,
      'name' => CRM_Booking_Utils_Constants::OPTION_VALUE_CANCELLED,
    );
    $result = civicrm_api3('OptionValue', 'get', $params);

    $this->_cancelStatusId =  $cancelStatus = CRM_Utils_Array::value('value', CRM_Utils_Array::value($result['id'], $result['values']));

    if ($this->_values['status_id'] == $cancelStatus & ($this->_action != CRM_Core_Action::DELETE & $this->_action != CRM_Core_Action::VIEW)) {
      $bookingPayment = civicrm_api3('BookingPayment', 'get', array('booking_id' => $this->_id));
      if($bookingPayment['count'] > 0){
        CRM_Core_Error::statusBounce(E::ts('The requested booking record has already been cancelled'));
      }
    }

    $this->_values['payment_status'] =  CRM_Booking_BAO_Booking::getPaymentStatus($this->_id);
    $paymentStatus = $this->_values['payment_status'];
	  $this->assign('booking', $this->_values);

    //ResoveDefault
    CRM_Booking_BAO_Booking::resolveDefaults($this->_values);
    $title = $this->_values['title'];
    CRM_Utils_System::setTitle(E::ts('Update Booking') . " - $title");

    //get contribution record
    $this->associatedContribution($this->_id);
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => E::ts('Delete'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => E::ts('Cancel'),
          ),
        )
      );
    }
    elseif ($this->_action & CRM_Core_Action::VIEW){
      $this->addButtons(array(
          array(
            'type' => 'cancel',
            'name' => E::ts('Done'),
            'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            'isDefault' => TRUE,
          ),
        )
      );
    }
    else {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => E::ts('Save'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => E::ts('Cancel'),
          ),
        )
      );
    }


    if (($this->_action & CRM_Core_Action::DELETE) || ($this->_action & CRM_Core_Action::VIEW)) {
      return;
    }else{
      $this->addElement('checkbox', 'send_confirmation', E::ts('Send email?'));

      $fromEmailAddress = CRM_Core_OptionGroup::values('from_email_address');
      if (empty($fromEmailAddress)) {
        //redirect user to enter from email address.
        $url = CRM_Utils_System::url('civicrm/admin/options/from_email_address', 'group=from_email_address&action=add&reset=1');
        $status = E::ts("There is no valid from email address present. You can add here <a href='%1'>Add From Email Address.</a>", array(1 => $url));
        $session->setStatus($status, E::ts('Notice'));
      }
      else {
        foreach ($fromEmailAddress as $key => $email) {
          $fromEmailAddress[$key] = htmlspecialchars($fromEmailAddress[$key]);
        }
      }

      $this->add('select', 'from_email_address',
        E::ts('From Email Address'), array(
          '' => E::ts('- select -')) + $fromEmailAddress, FALSE
      );
      //header of email template
      $this->add('textarea', 'receipt_header_message', E::ts('Header'));
      //footer of email template
      $this->add('textarea', 'receipt_footer_message', E::ts('Footer'));

      if($this->_id){
        $contactDropdown =  array('' => E::ts('- select -'),
                                $this->_values['primary_contact_id'] => CRM_Contact_BAO_Contact::displayName($this->_values['primary_contact_id']));
        $paymentContacts = $contactDropdown;
        if(isset($this->_values['secondary_contact_id'])){
          $contactDropdown[$this->_values['secondary_contact_id']] =  CRM_Contact_BAO_Contact::displayName($this->_values['secondary_contact_id']);
          //add Both option for sending email to both contacts
          $contactDropdown[CRM_Booking_Utils_Constants::OPTION_BOTH_CONTACTS] =  E::ts('Both');
          //$paymentContacts = array_slice($contactDropdown, 1, -1);
        }
      }else{
        $contactDropdown = array(
          '' => E::ts('- select -'),
          '1' => E::ts('Primary contact'),
          '2' => E::ts('Secondary contact'),
          CRM_Booking_Utils_Constants::OPTION_BOTH_CONTACTS => E::ts('Both')
        );
        //$paymentContacts = array_slice($contactDropdown, 1, -1);
      }

      $this->add('select', 'email_to', E::ts('Email to'),
        $contactDropdown, FALSE,
        array(
          'id' => 'email_to',
        )
      );

      $this->addElement('checkbox', 'record_contribution', E::ts('Record Payment?'));



      if(array_key_exists('both_contacts', $contactDropdown)){
        unset($contactDropdown['both_contacts']);
      }
      $paymentContacts = $contactDropdown;
      $this->add('select', 'select_payment_contact', E::ts('Select contact'),
          $paymentContacts, FALSE,
          array(
            'id' => 'select_payment_contact',
          )
      );


      $this->addDate('receive_date', E::ts('Received'), FALSE, array('formatType' => 'activityDate'));

      if ($this->_action & CRM_Core_Action::CLOSE){
        $this->add('text', 'total_amount', E::ts('Amount'));
      }else{
        $this->add('text', 'total_amount', E::ts('Amount'), array( 'disabled' => 'disabled' ));
      }

      $this->addRule("total_amount", E::ts('Please enter a valid amount.'), 'money');
      
      $this->add('select', 'financial_type_id',
        E::ts('Financial Type'),
        array('' => E::ts('- select -')) + CRM_Contribute_PseudoConstant::financialType()
      );

      $this->add('select', 'payment_instrument_id',
          E::ts('Paid By'),
          array('' => E::ts('- select -')) + CRM_Contribute_PseudoConstant::paymentInstrument(),
          FALSE,
          array('onChange' => "return showHideByValue('payment_instrument_id','4','checkNumber','table-row','select',false);")
      );

      $this->add('text', 'check_number', E::ts('Check Number'));
      
      $this->add('text', 'trxn_id', E::ts('Transaction ID'));

      $this->add('select', 'contribution_status_id',
          E::ts('Payment Status'),
          array('' => E::ts('- select -')) + CRM_Contribute_PseudoConstant::contributionStatus(),
          FALSE,
          array()
      );

      $this->addElement('checkbox', 'include_payment_information', '', E::ts(' Include payment information on booking confirmation email?'));

    }
  }


  protected static function rules($params, $files, $self) {
    $errors = array();
    $secondaryContactId = CRM_Utils_Array::value('secondary_contact_id', $params);
    $sendConfirmation = CRM_Utils_Array::value('send_confirmation', $params);
    if($sendConfirmation){
       $emailTo = CRM_Utils_Array::value('email_to', $params);
       if(!$emailTo){
        $errors['email_to'] = E::ts('Please select a contact(s) to send email to.');
       }
      if(!$self->_id){
        if($emailTo == 2 && !$secondaryContactId || $emailTo == 3 && !$secondaryContactId ){
          $errors['email_to'] = E::ts('Please select a secondary contact.');
        }
      }
      $fromEmailAddreess = CRM_Utils_Array::value('from_email_address', $params);
        if(!$fromEmailAddreess){
          $errors['from_email_address'] = E::ts('Please select a from email address.');
      }
     }


     $recordContribution = CRM_Utils_Array::value('record_contribution', $params);
     if($recordContribution){
        $selectPaymentContact = CRM_Utils_Array::value('select_payment_contact', $params);
        if(!$selectPaymentContact){
          $errors['select_payment_contact'] = E::ts('Please select a contact for recording payment.');
        }
        if(!$self->_id){
          if($selectPaymentContact == 2 && !$secondaryContactId){
            $errors['select_payment_contact'] = E::ts('Please select a contact for recording payment');
          }
        }
        $financialTypeId = CRM_Utils_Array::value('financial_type_id', $params);
        if(!$financialTypeId){
         $errors['financial_type_id'] = E::ts('Please select a financial type.');
        }

        $trxnId = CRM_Utils_Array::value('trxn_id', $params);
        $duplicates = array();
        if($trxnId && CRM_Contribute_BAO_Contribution::checkDuplicate(array('trxn_id' => $trxnId), $duplicates)){
          $d = implode(', ', $duplicates);
          $errors['trxn_id'] = "Duplicate error - existing contribution record(s) have a matching Transaction ID. Contribution record ID is: $d";
        }
        $receivedDate = CRM_Utils_Array::value('receive_date', $params);
        if(!$receivedDate){
         $errors['receive_date'] = E::ts('This field is required.');
        }

        $paymentInstrumentId = CRM_Utils_Array::value('payment_instrument_id', $params);
        if(!$paymentInstrumentId){
         $errors['payment_instrument_id'] = E::ts('Please select a payment instrument.');
        }

        $contributionStatusId = CRM_Utils_Array::value('contribution_status_id', $params);
        if(!$contributionStatusId){
         $errors['contribution_status_id'] = E::ts('Please select a valid payment status.');
        }

     }
    return $errors;
  }


   /**
   * This function sets the default values for the form. that in edit mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    if (($this->_action & CRM_Core_Action::DELETE) || ($this->_action & CRM_Core_Action::VIEW)) {
      return;
    }else{
      $defaults = array();
      try{
        $bookingPayment = civicrm_api3('BookingPayment', 'get', array('booking_id' => $this->_id));
        $payment = CRM_Utils_Array::value($this->_id, $bookingPayment['values']);
      }
      catch (CiviCRM_API3_Exception $e) {
        //display error message?
        CRM_Core_Session::setStatus( $e->getMessage(), E::ts('Error'), 'error');
      }
      if(!empty($payment) && isset($payment['contribution_id'])){ //payment exist
        $defaults['record_contribution'] = 1;
        $params = array(
          'version' => 3,
          'id' => $payment['contribution_id'],
        );
        $result = civicrm_api('Contribution', 'get', $params);
        $contribution = CRM_Utils_Array::value($payment['contribution_id'], $result['values'] );
        $defaults['select_payment_contact'] = $contribution['contact_id'];
        //$defaults['receive_date'] = $contribution['receive_date']; //fixed received date
        $defaults['total_amount'] = $contribution['total_amount'];
        $defaults['trxn_id'] = $contribution['trxn_id'];
        $defaults['financial_type_id'] = $contribution['financial_type_id'];
        //TODO:: the instrument id return wrong value
        $defaults['payment_instrument_id'] = $contribution['instrument_id'];
        $defaults['contribution_status_id'] = $contribution['contribution_status_id'];
      }else{
          $defaults['total_amount'] =CRM_Utils_Array::value('total_amount', $this->_values);
      }
      if ($this->_action & CRM_Core_Action::CLOSE){
        $defaults = $this->_values;
      }
      return $defaults;

    }
  }

  function postProcess(){
    //CRM_Utils_System::flushCache();

    if ($this->_action & CRM_Core_Action::ADD || $this->_action & CRM_Core_Action::UPDATE || $this->_action & CRM_Core_Action::CLOSE) {
      $bookingInfo = $this->exportValues();

      if(CRM_Utils_Array::value('record_contribution', $bookingInfo)){ //TODO:: Check if contribution exist
        $values = array();
        if ($this->_action & CRM_Core_Action::ADD){
          if(CRM_Utils_Array::value('select_payment_contact', $bookingInfo) == 1){
            $values['payment_contact'] =  CRM_Utils_Array::value('primary_contact_id', $bookingInfo);
          }else if(CRM_Utils_Array::value('select_payment_contact', $bookingInfo) == 2){
            $values['payment_contact'] =  CRM_Utils_Array::value('secondary_contact_id', $bookingInfo);
          }else{
            $values['payment_contact'] =  CRM_Utils_Array::value('primary_contact_id', $bookingInfo);
          }
        }else{
            $values['payment_contact'] =  CRM_Utils_Array::value('select_payment_contact', $bookingInfo);
        }

        $values['total_amount'] = CRM_Utils_Array::value('total_amount', $bookingInfo);
        $values['booking_id'] = $this->_id;
        $values['receive_date'] = CRM_Utils_Date::processDate(CRM_Utils_Array::value('receive_date', $bookingInfo));
        $values['financial_type_id'] = CRM_Utils_Array::value('financial_type_id', $bookingInfo);
        $values['payment_instrument_id'] = CRM_Utils_Array::value('payment_instrument_id', $bookingInfo);
        $values['check_number'] = CRM_Utils_Array::value('check_number', $bookingInfo);
        $values['trxn_id'] = CRM_Utils_Array::value('trxn_id', $bookingInfo);
        //Payment status is a contribution status
        $values['contribution_status_id'] = CRM_Utils_Array::value('contribution_status_id', $bookingInfo);
        $values['booking_title'] = CRM_Utils_Array::value('title', $this->_values);
        CRM_Booking_BAO_Booking::recordContribution($values);
      }

      $sendConfirmation = CRM_Utils_Array::value('send_confirmation', $bookingInfo);
      if($sendConfirmation){ //check sending email parameter
        $values = array();
        $fromEmailAddress = CRM_Core_OptionGroup::values('from_email_address');
        $values['from_email_address'] = CRM_Utils_Array::value(CRM_Utils_Array::value('from_email_address', $bookingInfo), $fromEmailAddress);
        $values['booking_id'] = $this->_id;
        $values['primary_contact_id'] = CRM_Utils_Array::value('primary_contact_id', $this->_values);
        $values['secondary_contact_id'] = CRM_Utils_Array::value('secondary_contact_id', $this->_values);
        $values['booking_title'] = $this->_values['title'];
        $values['booking_status'] = CRM_Utils_Array::value('status',$this->_values);;
        $values['booking_date'] = $this->_values['booking_date'];
        $values['booking_start_date'] = $this->_values['start_date'];
        $values['booking_end_date'] = $this->_values['end_date'];
        $values['participants_estimate'] = CRM_Utils_Array::value('participants_estimate',$this->_values);
        $values['participants_actual'] = CRM_Utils_Array::value('participants_actual',$this->_values);
        $values['receipt_header_message'] = CRM_Utils_Array::value('receipt_header_message',$bookingInfo);
        $values['receipt_footer_message'] = CRM_Utils_Array::value('receipt_footer_message',$bookingInfo);
        $values['include_payment_info'] = CRM_Utils_Array::value('include_payment_information', $bookingInfo);

        $emailTo = CRM_Utils_Array::value('email_to', $bookingInfo);  //retrieve value from 'Email to' selectbox
        $contactIds = array();
        if ($this->_action & CRM_Core_Action::ADD){
          if($emailTo == 1){
            array_push($contactIds, CRM_Utils_Array::value('primary_contact_id', $bookingInfo));
          }elseif ($emailTo == 2){
            array_push($contactIds, CRM_Utils_Array::value('secondary_contact_id', $bookingInfo));
          }else{
            array_push($contactIds, CRM_Utils_Array::value('primary_contact_id', $bookingInfo));
            array_push($contactIds, CRM_Utils_Array::value('secondary_contact_id', $bookingInfo));
          }
        }else{
          if($emailTo == CRM_Booking_Utils_Constants::OPTION_BOTH_CONTACTS){
            array_push($contactIds, CRM_Utils_Array::value('primary_contact_id', $this->_values));
            array_push($contactIds, CRM_Utils_Array::value('secondary_contact_id', $this->_values));
          }else{
            array_push($contactIds, $emailTo);
          }
        }

        foreach ($contactIds as $key => $cid) {
          $return = CRM_Booking_BAO_Booking::sendMail($cid, $values);   //send email
        }


      }
      $params = array(
          'id' => $this->_id,
          'target_contact_id' => CRM_Utils_Array::value('primary_contact_id', $bookingInfo),
          'subject' => E::ts("Booking ID: $this->_id")
      );

      //Finally add booking activity
     CRM_Booking_BAO_Booking::createActivity($params);
    }
  }

  /**
   * This function is used for the to show the associated
   * contribution for the booking
   * return null
   */
  function associatedContribution($booking_id = NULL) {
    //get contributionId from booking_payment
    $contributionId = NULL;
    $bookingPaymentResult = civicrm_api3('BookingPayment','get',array('booking_id'=>$booking_id,));
    if($bookingPaymentResult['count'] == 0){
        return NULL;
    }else{
        $bookingPaymentValues = CRM_Utils_Array::value('values', $bookingPaymentResult);
        $contributionId = CRM_Utils_Array::value('contribution_id',current($bookingPaymentValues));
    }

    //get contribution record by selector controller framework
    //REMARK: consider CiviCRM CORE dependency
    $this->_formValues['contribution_id'] = $contributionId;
    $this->_queryParams = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);
    $selector = new CRM_Contribute_Selector_Search($this->_queryParams,
      NULL, NULL, NULL, NULL, NULL
    );

    $controller = new CRM_Core_Selector_Controller($selector,
      NULL, NULL,
      CRM_Core_Action::VIEW,
      $this,
      CRM_Core_Selector_Controller::TRANSFER, NULL
    );
    $controller->setEmbedded(TRUE);
    $controller->run();
  }
}

