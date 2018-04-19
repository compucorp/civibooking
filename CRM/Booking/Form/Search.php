<?php
use CRM_Booking_ExtensionUtil as E; 

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Booking_Form_Search extends CRM_Core_Form {


  /**
   * Are we forced to run a search
   *
   * @var int
   * @access protected
   */
  protected $_force;

  /**
   * name of search button
   *
   * @var string
   * @access protected
   */
  protected $_searchButtonName;

  /**
   * name of print button
   *
   * @var string
   * @access protected
   */
  protected $_printButtonName;

  /**
   * name of action button
   *
   * @var string
   * @access protected
   */
  protected $_actionButtonName;

  /**
   * form values that we will be using
   *
   * @var array
   * @access public
   */
  public $_formValues;

  /**
   * the params that are sent to the query
   *
   * @var array
   * @access protected
   */
  protected $_queryParams;

  /**
   * have we already done this search
   *
   * @access protected
   * @var boolean
   */
  protected $_done;

  /**
   * are we restricting ourselves to a single contact
   *
   * @access protected
   * @var boolean
   */
  protected $_single = FALSE;

  /**
   * are we restricting ourselves to a single contact
   *
   * @access protected
   * @var boolean
   */
  protected $_limit = NULL;

  /**
   * what context are we being invoked from
   *
   * @access protected
   * @var string
   */
  protected $_context = NULL;

  public $_defaults;

  /**
   * prefix for the controller
   *
   */
  protected $_prefix = "booking_";

  /**
   * processing needed for buildForm and later
   *
   * @return void
   * @access public
   */

  function preProcess() {


    $this->set('searchFormName', 'Search');
    /**
     * set the button names
     */
    $this->_searchButtonName = $this->getButtonName('refresh');
    $this->_printButtonName = $this->getButtonName('next', 'print');
    $this->_actionButtonName = $this->getButtonName('next', 'action');

    $this->_done = FALSE;
    $this->defaults = array();

  /*
     * we allow the controller to set force/reset externally, useful when we are being
     * driven by the wizard framework
     */
    $this->_reset   = CRM_Utils_Request::retrieve('reset', 'Boolean', CRM_Core_DAO::$_nullObject);
    $this->_force   = CRM_Utils_Request::retrieve('force', 'Boolean', $this, FALSE);
    $this->_limit   = CRM_Utils_Request::retrieve('limit', 'Positive', $this);
    $this->_context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'search');
    $this->_ssID    = CRM_Utils_Request::retrieve('ssID', 'Positive', $this);
    $this->assign("context", $this->_context);


    // get user submitted values
    // get it from controller only if form has been submitted, else preProcess has set this
    if (!empty($_POST)) {
      $this->_formValues = $this->controller->exportValues($this->_name);
    }
    else {
      $this->_formValues = $this->get('formValues');
    }

    if ($this->_force) {
      $this->postProcess();
      $this->set('force', 0);
    }

    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
        $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }

    //$this->_queryParams = CRM_Booking_BAO_BookingContactQuery::convertFormValues($this->_formValues);
   // $this->_queryParams = CRM_Booking_BAO_Query::convertFormValues($this->_formValues);
    $this->_queryParams = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);


    $selector = new CRM_Booking_Selector_Search($this->_queryParams,
      $this->_action,
      NULL,
      $this->_single,
      $this->_limit,
      $this->_context
    );


    $prefix = NULL;
    if ($this->_context == 'user') {
      $prefix = $this->_prefix;
    }

    $this->assign("{$prefix}limit", $this->_limit);
    $this->assign("{$prefix}single", $this->_single);

    $controller = new CRM_Core_Selector_Controller($selector,
      $this->get(CRM_Utils_Pager::PAGE_ID),
      $sortID,
      CRM_Core_Action::VIEW,
      $this,
      CRM_Core_Selector_Controller::TRANSFER,
      $prefix
    );

    $controller->setEmbedded(TRUE);
    $controller->moveFromSessionToTemplate();


    //$this->assign('bookingSummary', $this->get('summary'));
  }

   /**
   * Set the default form values
   *
   * @access protected
   *
   * @return array the default array reference
   */
  function setDefaultValues() {
    $defaults = array();
    $defaults = $this->_formValues;
    return $defaults;
  }


  /**
   * Build the form
   *
   * @access public
   *
   * @return void
   */
  function buildQuickForm() {
    // text for sort_name
    $this->addElement('text',
      'sort_name',
      E::ts('Contact Name or Email'),
      CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact',
        'sort_name'
      )
    );

    CRM_Booking_BAO_Query::buildSearchForm($this);

    /*
     * add form checkboxes for each row. This is needed out here to conform to QF protocol
     * of all elements being declared in builQuickForm
     */

    $rows = $this->get('rows');
    if (is_array($rows)) {
      if (!$this->_single) {
        $this->addElement('checkbox',
          'toggleSelect',
          NULL,
          NULL,
          array('onclick' => "toggleTaskAction( true ); return toggleCheckboxVals('mark_x_',this);")
        );
        foreach ($rows as $row) {
          $this->addElement('checkbox', $row['checkbox'],
            NULL, NULL,
            array('onclick' => "toggleTaskAction( true ); return checkSelectedBox('" . $row['checkbox'] . "');")
          );
        }
      }

      $total = $cancel = 0;

      $permission = CRM_Core_Permission::getPermission();

      $tasks = array('' => E::ts('- actions -')) + CRM_Booking_Form_Task::permissionedTaskTitles($permission);
      $this->add('select', 'task', E::ts('Actions:') . ' ', $tasks);
      $this->add('submit', $this->_actionButtonName, E::ts('Go'),
        array(
          'class' => 'form-submit',
          'id' => 'Go',
          'onclick' => "return checkPerformAction('mark_x', '" . $this->getName() . "', 0);",
        )
      );

      //-- Remove print button as it causes an exception when pressed
      //-- TODO : Bring back search results print button functionality (See: PCBK-182)
      // $this->add('submit', $this->_printButtonName, E::ts('Print'),
        // array(
          // 'class' => 'form-submit',
          // 'onclick' => "return checkPerformAction('mark_x', '" . $this->getName() . "', 1);",
        // )
      // );

      // need to perform tasks on all or selected items ? using radio_ts(task selection) for it
      $this->addElement('radio', 'radio_ts', NULL, '', 'ts_sel', array('checked' => 'checked'));
      $this->addElement('radio', 'radio_ts', NULL, '', 'ts_all', array('onclick' => $this->getName() . ".toggleSelect.checked = false; toggleCheckboxVals('mark_x_',this); toggleTaskAction( true );"));
    }


    // add buttons
    $this->addButtons(array(
        array(
          'type' => 'refresh',
          'name' => E::ts('Search'),
          'isDefault' => TRUE,
        ),
      )
    );
  }

  /**
   * The post processing of the form gets done here.
   *
   * Key things done during post processing are
   *      - check for reset or next request. if present, skip post procesing.
   *      - now check if user requested running a saved search, if so, then
   *        the form values associated with the saved search are used for searching.
   *      - if user has done a submit with new values the regular post submissing is
   *        done.
   * The processing consists of using a Selector / Controller framework for getting the
   * search results.
   *
   * @param
   *
   * @return void
   * @access public
   */
  function postProcess() {
    if ($this->_done) {
      return;
    }
    $this->_done = TRUE;

    if (!empty($_POST)) {
      $this->_formValues = $this->controller->exportValues($this->_name);
    }

    $this->fixFormValues();


    $this->_queryParams = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);


    $this->set('formValues', $this->_formValues);
    $this->set('queryParams', $this->_queryParams);


    $buttonName = $this->controller->getButtonName();
    if ($buttonName == $this->_actionButtonName || $buttonName == $this->_printButtonName) {
      // check actionName and if next, then do not repeat a search, since we are going to the next page

      // hack, make sure we reset the task values
      $stateMachine = $this->controller->getStateMachine();
      $formName = $stateMachine->getTaskFormName();
      $this->controller->resetPage($formName);
      return;
    }


    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
      $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }

    $this->_queryParams = CRM_Contact_BAO_Query::convertFormValues($this->_formValues);

    $selector = new CRM_Booking_Selector_Search($this->_queryParams,
      $this->_action,
      NULL,
      $this->_single,
      $this->_limit,
      $this->_context
    );


    $selector->setKey($this->controller->_key);


    $prefix = NULL;
    if ($this->_context == 'basic' || $this->_context == 'user') {
      $prefix = $this->_prefix;
    }

    $controller = new CRM_Core_Selector_Controller(
      $selector,
      $this->get(CRM_Utils_Pager::PAGE_ID),
      $sortID,
      CRM_Core_Action::VIEW,
      $this,
      CRM_Core_Selector_Controller::SESSION,
      $prefix
    );
    $controller->setEmbedded(TRUE);


    $query = &$selector->getQuery();
    if ($this->_context == 'user') {
    }
    /*
    $summary = &$query->summaryBooking($this->_context);
    $this->set('summary', $summary);
    $this->assign('bookingSummary', $summary);*/
    $controller->run();

  }

  function fixFormValues() {
    // if this search has been forced
    // then see if there are any get values, and if so over-ride the post values
    // note that this means that GET over-rides POST :)
    if (!$this->_force) {
      return;
    }

    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this);

    if ($cid) {
      $cid = CRM_Utils_Type::escape($cid, 'Integer');
      if ($cid > 0) {
        $this->_formValues['contact_id'] = $cid;

        // also assign individual mode to the template
        $this->_single = TRUE;
      }
    }


    //give values to default.
    $this->_defaults = $this->_formValues;
  }




  /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return E::ts('Find Bookings');
  }

}
