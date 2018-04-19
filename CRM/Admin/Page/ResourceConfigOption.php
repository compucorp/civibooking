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
 * Page for displaying list of resources
 */
class CRM_Admin_Page_ResourceConfigOption extends CRM_Core_Page_Basic {

  /**
   * The action links that we need to display for the browse screen
   *
   * @var array
   * @static
   */
  static $_links = NULL;


  protected $_sid = NULL;

  /**
   * Get BAO Name
   *
   * @return string Classname of BAO.
   */
  function getBAOName() {
    return 'CRM_Booking_BAO_ResourceConfigOption';
  }

  /**
   * Get action Links
   *
   * @return array (reference) of action links
   */
  function &links() {
    if (!(self::$_links)) {
      self::$_links = array(
        CRM_Core_Action::UPDATE => array(
          'name' => E::ts('Edit'),
          'url' => 'civicrm/admin/resource/config_set/config_option',
          'qs' => 'action=update&id=%%id%%&sid=%%sid%%&reset=1',
          'title' => E::ts('Edit Resource Configuration Option'),
        ),
        CRM_Core_Action::DISABLE => array(
          'name' => E::ts('Disable'),
          'ref' => 'crm-enable-disable',
          'title' => E::ts('Disable Resource Configuration Option'),
        ),
        CRM_Core_Action::ENABLE => array(
          'name' => E::ts('Enable'),
          'ref' => 'crm-enable-disable',
          'title' => E::ts('Enable Resource Configuration Option'),
        ),
        CRM_Core_Action::DELETE => array(
          'name' => E::ts('Delete'),
          'url' => 'civicrm/admin/resource/config_set/config_option',
          'qs' => 'action=delete&id=%%id%%&sid=%%sid%%',
          'title' => E::ts('Delete Resource Configuration Option'),
        ),
      );
    }
    return self::$_links;
  }

  /**
   * Run the page.
   *
   * This method is called after the page is created. It checks for the
   * type of action and executes that action.
   * Finally it calls the parent's run method.
   *
   * @return void
   * @access public
   *
   */
  function run() {
    $this->_sid = CRM_Utils_Request::retrieve('sid', 'Positive',
      $this, FALSE, 0
    );
    $this->assign('sid', $this->_sid);
    // set title and breadcrumb
    CRM_Utils_System::setTitle(E::ts('Settings - Resource Configuration Option'));
    /*$breadCrumb = array(array('title' => E::ts('Administration'),
        'url' => CRM_Utils_System::url('civicrm/admin',
        'reset=1'
        ),
      ));
    CRM_Utils_System::appendBreadCrumb($breadCrumb); */
    return parent::run();
  }

  /**
   * Browse all resources.
   *
   * @return void
   * @access public
   * @static
   */
  function browse($action = NULL) {
    $units =  CRM_Booking_DAO_ResourceConfigOption::buildOptions('unit_id', 'create');

    // get all config option sorted by weight
    $configOptions = array();
    $dao = new CRM_Booking_DAO_ResourceConfigOption();
    $dao->set_id = $this->_sid;
    $dao->orderBy('weight');
    $dao->is_deleted = FALSE;
    $dao->find();

    while ($dao->fetch()) {

      $configOptions[$dao->id] = array();
      CRM_Core_DAO::storeValues($dao, $configOptions[$dao->id]);
      $configOptions[$dao->id]['unit'] =  CRM_Utils_Array::value(CRM_Utils_Array::value('unit_id', $configOptions[$dao->id]), $units);

      // form all action links
      $action = array_sum(array_keys($this->links()));

      // update enable/disable links.
      if ($dao->is_active) {
        $action -= CRM_Core_Action::ENABLE;
      }
      else {
        $action -= CRM_Core_Action::DISABLE;
      }

      $configOptions[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action,
        array('id' => $dao->id,
              'sid' => $this->_sid
              )
      );

    }

    $this->assign('rows', $configOptions);
  }

  /**
   * Get name of edit form
   *
   * @return string Classname of edit form.
   */
  function editForm() {
    return 'CRM_Admin_Form_ResourceConfigOption';
  }

  /**
   * Get edit form name
   *
   * @return string name of this page.
   */
  function editName() {
    return 'ResourceConfigOption';
  }

  /**
   * Get user context.
   *
   * @return string user context.
   */
  function userContext($mode = NULL) {
    return 'civicrm/admin/resource/config_set/config_option';
  }
}

