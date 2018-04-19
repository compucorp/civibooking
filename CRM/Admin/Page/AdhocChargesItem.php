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
 *
 */

/**
 * This class generates form components for Resource
 *
 */
class CRM_Admin_Page_AdhocChargesItem extends CRM_Core_Page_Basic {

  /**
   * The action links that we need to display for the browse screen
   *
   * @var array
   * @static
   */
  static $_links = NULL;

  /**
   * Get BAO Name
   *
   * @return string Classname of BAO.
   */
  function getBAOName() {
    return 'CRM_Booking_BAO_AdhocChargesItem';
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
          'url' => 'civicrm/admin/adhoc_charges_item',
          'qs' => 'action=update&id=%%id%%&reset=1',
          'title' => E::ts('Edit Additional Charges Item'),
        ),
        CRM_Core_Action::DISABLE => array(
          'name' => E::ts('Disable'),
          'ref' => 'crm-enable-disable',
          'title' => E::ts('Disable Additional Charges Item'),
        ),
        CRM_Core_Action::ENABLE => array(
          'name' => E::ts('Enable'),
          'ref' => 'crm-enable-disable',
          'title' => E::ts('Enable Additional Charges Item'),
        ),
                
        CRM_Core_Action::DELETE => array(
          'name' => E::ts('Delete'),
          'url' => 'civicrm/admin/adhoc_charges_item',
          'qs' => 'action=delete&id=%%id%%',
          'title' => E::ts('Delete Additional Charges Item'),
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
    // set title
    CRM_Utils_System::setTitle(E::ts('Settings - Additional Charges Item '));
   
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
  	 
    // get all custom groups sorted by weight
    $adhoc_charges_item = array();
    $dao = new CRM_Booking_DAO_AdhocChargesItem();
    $dao->orderBy('weight');
    $dao->is_deleted = FALSE;
    $dao->find();

    while ($dao->fetch()) {
      $adhoc_charges_item[$dao->id] = array();
      CRM_Core_DAO::storeValues($dao, $adhoc_charges_item[$dao->id]);
      
      // form all action links
      $action = array_sum(array_keys($this->links()));

      // update enable/disable links.
      if ($dao->is_active) {
        $action -= CRM_Core_Action::ENABLE;
      }
      else {
        $action -= CRM_Core_Action::DISABLE;
      }

      $adhoc_charges_item[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action,
        array('id' => $dao->id)
      );

    }
    $this->assign('rows', $adhoc_charges_item);
  }

  /**
   * Get name of edit form
   *
   * @return string Classname of edit form.
   */
  function editForm() {
    return 'CRM_Admin_Form_AdhocChargesItem';
  }

  /**
   * Get edit form name
   *
   * @return string name of this page.
   */
  function editName() {
//    return 'Resource';
    return 'Adhoc Charges Item';
  }

  /**
   * Get user context.
   *
   * @return string user context.
   */
  function userContext($mode = NULL) {
    return 'civicrm/admin/adhoc_charges_item';
  }
}
