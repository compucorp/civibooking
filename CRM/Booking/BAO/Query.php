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
class CRM_Booking_BAO_Query extends CRM_Contact_BAO_Query_Interface{


  /**
   * static field for all the export/import booking fields
   *
   * @var array
   * @static
   */
  static $_bookingFields = array();


  function &getFields() {
    if (!self::$_bookingFields) {
      self::$_bookingFields = array_merge(self::$_bookingFields, CRM_Booking_DAO_Booking::export());
    }
    return self::$_bookingFields;
  }

  /**
   * build select for CiviBooking
   *
   * @return void
   * @access public
   */
  function select(&$query) {
    if(  CRM_Contact_BAO_Query::componentPresent($query->_returnProperties, 'booking_')) {
        $fields = $this->getFields();
       foreach ($fields as $fldName => $params) {
          if (CRM_Utils_Array::value($fldName, $query->_returnProperties)) {
            $query->_select[$fldName]  = "{$params['where']} as $fldName";
            $query->_element[$fldName] = 1;
            list($tableName, $dnc) = explode('.', $params['where'], 2);
            $query->_tables[$tableName]  = $query->_whereTables[$tableName] = 1;
          }
        }

      if (CRM_Utils_Array::value('booking_status', $query->_returnProperties) ||
        CRM_Utils_Array::value('booking_status_id', $query->_returnProperties)
      ) {
        $query->_select['civicrm_booking_status'] = "civicrm_booking_status.label as booking_status";
        $query->_select['civicrm_booking_status_id'] = "civicrm_booking.status_id as booking_status_id";
        $query->_element['civicrm_booking_status_id'] = 1;
        $query->_element['civicrm_booking_status'] = 1;
        $query->_tables['civicrm_booking'] = 1;
        $query->_tables['civicrm_booking_status'] = 1;

      }

      if (CRM_Utils_Array::value('booking_payment_status', $query->_returnProperties) ||
        CRM_Utils_Array::value('booking_payment_status_id', $query->_returnProperties)
      ) {
        $query->_select['civicrm_booking_payment_status'] = "civicrm_booking_payment_status.label as booking_payment_status";
        $query->_select['civicrm_booking_payment_status_id'] = "civicrm_booking_payment_status.value as booking_payment_status_id";
        $query->_element['civicrm_booking_payment_status_id'] = 1;
        $query->_element['civicrm_booking_payment_status'] = 1;
        $query->_tables['civicrm_booking'] = 1;
        $query->_tables['civicrm_booking_payment_status'] = 1;

      }

      if (CRM_Utils_Array::value('booking_title', $query->_returnProperties)) {
        $query->_select['booking_title'] = "civicrm_booking.title as booking_title";
        $query->_element['booking_title'] = 1;
      }


      if (CRM_Utils_Array::value('booking_created_date', $query->_returnProperties)) {
        $query->_select['booking_created_date'] = "civicrm_booking.created_date as booking_created_date";
        $query->_element['booking_created_date'] = 1;
      }

      if (CRM_Utils_Array::value('booking_total_amount', $query->_returnProperties)) {
        $query->_select['booking_total_amount'] = "civicrm_booking.total_amount as booking_total_amount";
        $query->_element['booking_total_amount'] = 1;
      }

      if (CRM_Utils_Array::value('booking_event_date', $query->_returnProperties)) {
        $query->_select['booking_event_date'] = "civicrm_booking.booking_date as booking_event_date";
        $query->_element['booking_event_date'] = 1;
      }
	  if (CRM_Utils_Array::value('booking_start_date', $query->_returnProperties)) {
        $query->_select['booking_start_date'] = "civicrm_booking.start_date as booking_start_date";
        $query->_element['booking_start_date'] = 1;
      }
	  if (CRM_Utils_Array::value('booking_end_date', $query->_returnProperties)) {
        $query->_select['booking_end_date'] = "civicrm_booking.end_date as booking_end_date";
        $query->_element['booking_end_date'] = 1;
      }
      if (CRM_Utils_Array::value('booking_associated_contact', $query->_returnProperties)) {
        $query->_select['booking_associated_contact'] = "civicrm_booking_associated_contact.sort_name as booking_associated_contact";
        $query->_select['booking_associated_contact_id'] = "civicrm_booking.secondary_contact_id as booking_associated_contact_id";
        $query->_element['civicrm_booking_associated_contact'] = 1;
        $query->_element['civicrm_booking_associated_contact_id'] = 1;
        $query->_tables['civicrm_contact'] = 1;
        $query->_tables['civicrm_booking_associated_contact'] = 1;
      }
    }
  }

  function where(&$query) {
    $grouping = NULL;
    foreach (array_keys($query->_params) as $id) {
      if (!CRM_Utils_Array::value(0, $query->_params[$id])) {
        continue;
      }
      if (substr($query->_params[$id][0], 0, 8) == 'booking_') {
        if ($query->_mode == CRM_Contact_BAO_QUERY::MODE_CONTACTS) {
          $query->_useDistinct = TRUE;
        }
        $this->whereClauseSingle($query->_params[$id], $query);
      }
    }
  }

  function whereClauseSingle(&$values, &$query) {
    $fields = $this->getFields();
    list($name, $op, $value, $grouping, $wildcard) = $values;
    $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
    switch ($name) {
      case 'booking_id':
        $query->_where[$grouping][] = CRM_Contact_BAO_Query::buildClause("civicrm_booking.id", $op, $value, 'String');
        $query->_qill[$grouping][] = E::ts("Booking ID %1 '%2'", array(1 => $op, 2 => $value));
        $query->_tables['civicrm_booking'] = $query->_whereTables['civicrm_booking'] = 1;
        return;
      case 'booking_po_no':
        $query->_where[$grouping][] = CRM_Contact_BAO_Query::buildClause("civicrm_booking.po_number", $op, $value, 'String');
        $query->_qill[$grouping][] = E::ts("Purchase Order Number %1 '%2'", array(1 => $op, 2 => $value));
        $query->_tables['civicrm_booking'] = $query->_whereTables['civicrm_booking'] = 1;
        return;
      case 'booking_status_id':
      case 'booking_payment_status_id':
        if (is_array($value)) {
          foreach ($value as $k => $v) {
            if ($v) {
              $val[$k] = $k;
            }
          }
          $status = array_values($val);
          $op = 'IN';
        }
        else {
          $op = '=';
          $status = $value;
        }
        if ($name == 'booking_payment_status_id'){
          $statusValues = CRM_Core_OptionGroup::values("contribution_status");
        }
        else {
          $statusValues = CRM_Core_OptionGroup::values(CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS);
        }

        $names = array();
        if (isset($val) &&
          is_array($val)
        ) {
          foreach ($val as $id => $dontCare) {
            $names[] = $statusValues[$id];
          }
        }
        else {
          $names[] = $statusValues[$value];
        }
        if ($name == 'booking_payment_status_id'){
          $query->_qill[$grouping][] = E::ts('Payment Status %1', array(1 => $op)) . ' ' . implode(' ' . E::ts('or') . ' ', $names);
          $query->_where[$grouping][] = CRM_Contact_BAO_Query::buildClause("civicrm_contribution.contribution_status_id",
            $op,
            $status,
            "Integer"
          );
          $query->_tables['civicrm_booking'] = $query->_whereTables['civicrm_booking'] = 1;
          $query->_tables['civicrm_booking_payment'] = $query->_whereTables['civicrm_booking_payment'] = 1;
          $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;

        }else {
          $query->_qill[$grouping][] = E::ts('Status %1', array(1 => $op)) . ' ' . implode(' ' . E::ts('or') . ' ', $names);
          $query->_where[$grouping][] = CRM_Contact_BAO_Query::buildClause("civicrm_booking.status_id",
            $op,
            $status,
            "Integer"
          );
          $query->_tables['civicrm_booking'] = $query->_whereTables['civicrm_booking'] = 1;
        }
        return;
      case 'booking_event_date':
      case 'booking_event_date_low':
      case 'booking_event_date_high':
        $query->dateQueryBuilder($values,
          'civicrm_booking', 'booking_event_date', 'booking_date', 'Event Start Date'
        );
        return;
      case 'booking_start_date':
      case 'booking_start_date_low':
      case 'booking_start_date_high':
        $query->dateQueryBuilder($values,
          'civicrm_booking', 'booking_start_date', 'start_date', 'Start Date'
        );
        return;
      case 'booking_end_date':
      case 'booking_end_date_low':
      case 'booking_end_date_high':
        $query->dateQueryBuilder($values,
          'civicrm_booking', 'booking_end_date', 'end_date', 'End Date'
        );
        return;

      default:
        if (!isset($fields[$name])) {
          CRM_Core_Session::setStatus(E::ts(
              'We did not recognize the search field: %1.',
              array(1 => $name)
            )
          );
          return;
        }
        $whereTable = $fields[$name];
        $value      = trim($value);
        $dataType   = "String";

        if (in_array($name, array('booking_title')) &&
          strpos($value, '%') === FALSE) {
          $op = 'LIKE';
          $value = "%" . trim($value, '%') . "%";
          $quoteValue = "\"$value\"";
        }
        $wc = ($op != 'LIKE') ? "LOWER($whereTable[where])" : "$whereTable[where]";
        $query->_where[$grouping][] = CRM_Contact_BAO_Query::buildClause($wc, $op, $value, $dataType);
        $query->_qill [$grouping][] = "{$whereTable['title']} {$op} {$quoteValue}";
        list($tableName, $fieldName) = explode('.', $whereTable['where'], 2);
        $query->_tables[$tableName] = $query->_whereTables[$tableName] = 1;
    }
  }

  function from($name, $mode, $side) {
    $from = NULL;
    switch ($name) {
      case 'civicrm_booking':
        $from = " $side JOIN civicrm_booking ON civicrm_booking.primary_contact_id = contact_a.id AND civicrm_booking.is_deleted = 0 ";
        break;
      case 'civicrm_booking_status':
        $from = " $side JOIN civicrm_option_group option_group_booking_status ON (option_group_booking_status.name = 'booking_status') ";
        $from .= " $side JOIN civicrm_option_value civicrm_booking_status ON (civicrm_booking.status_id = civicrm_booking_status.value AND option_group_booking_status.id = civicrm_booking_status.option_group_id ) ";
        break;
      case 'civicrm_booking_payment_status':
        $from .= " $side JOIN civicrm_booking_payment on civicrm_booking_payment.booking_id = civicrm_booking.id ";
        $from .= " $side JOIN civicrm_contribution contribution on contribution.id = civicrm_booking_payment.contribution_id ";
        $from .= " $side JOIN civicrm_option_group option_group_booking_payment ON option_group_booking_payment.name = 'contribution_status' ";
        $from .= " $side JOIN civicrm_option_value civicrm_booking_payment_status ON (contribution.contribution_status_id = civicrm_booking_payment_status.value AND option_group_booking_payment.id = civicrm_booking_payment_status.option_group_id ) ";
        break;
      case 'civicrm_booking_associated_contact':
        $from = " $side JOIN civicrm_contact civicrm_booking_associated_contact ON (civicrm_booking_associated_contact.id = civicrm_booking.secondary_contact_id) ";
        break;
    }
    return $from;
  }

  /**
   * getter for the qill object
   *
   * @return string
   * @access public
   */
  function qill() {
    return (isset($this->_qill)) ? $this->_qill : "";
  }

  static function defaultReturnProperties() {
    $properties = array(
        'contact_type' => 1,
        'contact_sub_type' => 1,
        'sort_name' => 1,
        'display_name' => 1,
        'booking_title' => 1,
        'booking_status_id' => 1,
        'booking_payment_status_id' => 1,
        'booking_created_date' => 1,
        'booking_total_amount' => 1,
        'booking_event_date' => 1,
        'booking_start_date' => 1,
        'booking_end_date' => 1,
        'booking_associated_contact' => 1,
    );
    return $properties;
  }

  static function buildSearchForm(&$form) {


    $form->add('text', 'booking_po_no', E::ts('Purchase Order Number'));

    $resourceTypes =  CRM_Booking_BAO_Resource::getResourceTypes();
    $resources = array();
    foreach ($resourceTypes as $value) {
      $resources[$value['id']] = $value['label'];
    }
    $form->add('select', 'booking_resource_id', E::ts('Resource Type'),
      array('' => E::ts('- select -')) + $resources,
      FALSE,
      array()
    );

    $form->add('text', 'booking_id', E::ts('Booking ID'));
    $form->add('text', 'booking_title', E::ts('Booking Title'));

    CRM_Core_Form_Date::buildDateRange($form, 'booking_event_date', 1, '_low', '_high', E::ts('From'), FALSE);

    CRM_Core_Form_Date::buildDateRange($form, 'booking_start_date', 1, '_low', '_high', E::ts('From'), FALSE);
    CRM_Core_Form_Date::buildDateRange($form, 'booking_end_date', 1, '_low', '_high', E::ts('From'), FALSE);

    $bookingStatus =  CRM_Booking_BAO_Booking::buildOptions('status_id', 'create');
    foreach ($bookingStatus as $id => $name) {
      $form->_bookingStatus = &$form->addElement('checkbox', "booking_status_id[$id]", NULL, $name);
    }

    $paymentStatus = CRM_Contribute_PseudoConstant::contributionStatus();
    foreach ($paymentStatus as $id => $name) {
      $form->_paymentStatus = $form->addElement('checkbox', "booking_payment_status_id[$id]", NULL, $name);
    }

  }

  function searchAction(&$row, $id) {}

  function setTableDependency(&$tables) {
    $tables = array_merge(array('civicrm_booking' => 1), $tables);
  }

  public function getPanesMapper(&$panes) {
   // if (!CRM_Core_Permission::check('access Booking')) return;
    $panes['Bookings'] = 'civicrm_booking';
  }

  public function registerAdvancedSearchPane(&$panes) {
    //if (!CRM_Core_Permission::check('access Booking')) return;
    $panes['Bookings'] = 'booking';
  }

  public function buildAdvancedSearchPaneForm(&$form, $type) {
    //if (!CRM_Core_Permission::check('access Booking')) return;
    if ($type  == 'booking') {
      $form->add('hidden', 'hidden_booking', 1);
      self::buildSearchForm($form);
    }
  }

  public function setAdvancedSearchPaneTemplatePath(&$paneTemplatePathArray, $type) {
    //if (!CRM_Core_Permission::check('access Booking')) return;
    if ($type  == 'booking') {
      $paneTemplatePathArray['booking'] = 'CRM/Booking/Form/Search/Criteria.tpl';
    }
  }

  /**
   * Describe options for available for use in the search-builder.
   *
   * The search builder determines its options by examining the API metadata corresponding to each
   * search field. This approach assumes that each field has a unique-name (ie that the field's
   * unique-name in the API matches the unique-name in the search-builder).
   *
   * @param array $apiEntities list of entities whose options should be automatically scanned using API metadata
   * @param array $fieldOptions keys are field unique-names; values describe how to lookup the options
   *   For boolean options, use value "yesno". For pseudoconstants/FKs, use the name of an API entity
   *   from which the metadata of the field may be queried. (Yes - that is a mouthful.)
   * @void
   */
  public function alterSearchBuilderOptions(&$apiEntities, &$fieldOptions) {
   // if (!CRM_Core_Permission::check('access Booking')) return;
    $apiEntities = array_merge($apiEntities, array(
      'Booking',
      'BookingPayment',
      'Slot',
      'SubSlot',
    ));
  }


}

