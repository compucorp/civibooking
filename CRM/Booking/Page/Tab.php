<?php
use CRM_Booking_ExtensionUtil as E; 

require_once 'CRM/Core/Page.php';

class CRM_Booking_Page_Tab extends CRM_Core_Page {


  public $_permission = NULL;
  public $_contactId = NULL;

  /**
   * This function is called when action is browse
   *
   * return null
   * @access public
   */
  function browse() {
    $controller = new CRM_Core_Controller_Simple(
      'CRM_Booking_Form_Search',
      E::ts('Booking'),
      $this->_action
    );
    $controller->setEmbedded(TRUE);
    $controller->reset();
    $controller->set('cid', $this->_contactId);
    $controller->set('context', 'booking');
    $controller->process();
    $controller->run();

    if ($this->_contactId) {
      $displayName = CRM_Contact_BAO_Contact::displayName($this->_contactId);
      $this->assign('displayName', $displayName);
    }

    $bookings = CRM_Booking_BAO_Booking::getContactAssociatedBooking($this->_contactId);
    $this->assign('associatedBooking', $bookings);
  }


  /**
   * This function is called when action is view
   *
   * return null
   * @access public
   */
  function view() {

    $controller = new CRM_Core_Controller_Simple(
      'CRM_Booking_Form_Booking_View',
      E::ts('View Booking'),
      $this->_action
    );
    $controller->setEmbedded(TRUE);
    $controller->set('id', $this->_id);
    $controller->set('cid', $this->_contactId);

    return $controller->run();

  }

  /**
   * This function is called when action is edit or delete
   *
   * return null
   * @access public
   */
  function edit() {

    $hasUpdatePerm = CRM_Core_Permission::check('create and update bookings');
    $hasAdministerPerm = CRM_Core_Permission::check('administer CiviBooking');

    $canUpdate  = ($hasUpdatePerm || $hasAdministerPerm);
    $canDelete = $hasAdministerPerm;

    $actionAddOrUpdate = in_array($this->_action, array(CRM_Core_Action::UPDATE, CRM_Core_Action::ADD));
    $isActionDelete = ($this->_action == CRM_Core_Action::DELETE);

    if(($actionAddOrUpdate && !$canUpdate) || ($isActionDelete && !$canDelete)) {
      CRM_Utils_System::permissionDenied();
      CRM_Utils_System::civiExit();
    }

    $controller = new CRM_Core_Controller_Simple(
      'CRM_Booking_Form_Booking_Update',
      E::ts('Booking'),
      $this->_action
    );
    $controller->setEmbedded(TRUE);
    $controller->set('id', $this->_id);
    $controller->set('cid', $this->_contactId);

    return $controller->run();
  }


  /**
   * This function is called when action is cancel
   *
   * return null
   * @access public
   */
  function cancel() {
    if (!CRM_Core_Permission::check('administer CiviBooking')) {
      CRM_Utils_System::permissionDenied();
      CRM_Utils_System::civiExit();
    }
    
    $controller = new CRM_Core_Controller_Simple(
      'CRM_Booking_Form_Booking_Cancel',
      E::ts('Booking'),
      $this->_action
    );
    $controller->setEmbedded(TRUE);
    $controller->set('id', $this->_id);
    $controller->set('cid', $this->_contactId);

    return $controller->run();
  }



  function preProcess() {
    $context       = CRM_Utils_Request::retrieve('context', 'String', $this);
    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, FALSE, 'browse');
    $this->_id     = CRM_Utils_Request::retrieve('id', 'Positive', $this);

    if ($context == 'standalone') {
      $this->_action = CRM_Core_Action::ADD;
    }
    else {
      $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
      $this->assign('contactId', $this->_contactId);

      // check logged in url permission
      CRM_Contact_Page_View::checkUserPermission($this);

      // set page title
      CRM_Contact_Page_View::setTitle($this->_contactId);
    }

    $this->assign('action', $this->_action);

    if ($this->_permission == CRM_Core_Permission::EDIT && !CRM_Core_Permission::check('edit booking')) {
      // demote to view since user does not have edit booking rights
      $this->_permission = CRM_Core_Permission::VIEW;
      $this->assign('permission', 'view');
    }
  }

  /**
   * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
   *
   * return null
   * @access public
   */
  function run() {
    $this->preProcess();

    $this->setContext();

    if ($this->_action & (CRM_Core_Action::UPDATE | CRM_Core_Action::DELETE)) {
      $this->edit();
    }elseif ($this->_action & CRM_Core_Action::VIEW){
      $this->view();
    }elseif ($this->_action & CRM_Core_Action::CLOSE){
      $this->cancel();
    }else {
      $this->browse();
    }

    return parent::run();
  }


  function setContext() {
    $context = CRM_Utils_Request::retrieve('context',
      'String', $this, FALSE, 'search'
    );
    $compContext = CRM_Utils_Request::retrieve('compContext',
      'String', $this
    );

    $qfKey = CRM_Utils_Request::retrieve('key', 'String', $this);

    //validate the qfKey
    if (!CRM_Utils_Rule::qfKey($qfKey)) {
      $qfKey = NULL;
    }

    switch ($context) {
      case 'dashboard':
        //TODO:: Implement dashboard for booking
        $url = CRM_Utils_System::url('civicrm/booking', 'reset=1');
        break;

      case 'search':
        $urlParams = 'force=1';
        if ($qfKey) {
          $urlParams .= "&qfKey=$qfKey";
        }
        $this->assign('searchKey', $qfKey);

        if ($compContext == 'advanced') {
          $url = CRM_Utils_System::url('civicrm/contact/search/advanced', $urlParams);
        }
        else {
          $url = CRM_Utils_System::url('civicrm/booking/search', $urlParams);
        }
        break;

      case 'user':
        $url = CRM_Utils_System::url('civicrm/user', 'reset=1');
        break;

      case 'participant':
        $url = CRM_Utils_System::url('civicrm/contact/view',
          "reset=1&force=1&cid={$this->_contactId}&selectedChild=participant"
        );
        break;

      case 'home':
        $url = CRM_Utils_System::url('civicrm/dashboard', 'force=1');
        break;

      case 'activity':
        $url = CRM_Utils_System::url('civicrm/contact/view',
          "reset=1&force=1&cid={$this->_contactId}&selectedChild=activity"
        );
        break;

      case 'booking':
        $url = CRM_Utils_System::url('civicrm/contact/view',
          "reset=1&force=1&cid={$this->_contactId}&selectedChild=booking"
        );
        break;

      case 'standalone':
        $url = CRM_Utils_System::url('civicrm/dashboard', 'reset=1');
        break;

      case 'fulltext':
        $keyName   = '&qfKey';
        $urlParams = 'force=1';
        $urlString = 'civicrm/contact/search/custom';
        if ($this->_action == CRM_Core_Action::UPDATE) {
          if ($this->_contactId) {
            $urlParams .= '&cid=' . $this->_contactId;
          }
          $keyName = '&key';
          $urlParams .= '&context=fulltext&action=view';
          $urlString = 'civicrm/contact/view/participant';
        }
        if ($qfKey) {
          $urlParams .= "$keyName=$qfKey";
        }
        $this->assign('searchKey', $qfKey);
        $url = CRM_Utils_System::url($urlString, $urlParams);
        break;

      default:
        $cid = NULL;
        if ($this->_contactId) {
          $cid = '&cid=' . $this->_contactId;
        }
        $url = CRM_Utils_System::url('civicrm/booking/search',
          'force=1' . $cid
        );
        break;
    }
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext($url);
  }

}
