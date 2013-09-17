<?php
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
 * This class is a heart of search query building mechanism.
 */
class CRM_Booking_BAO_BookingContactQuery extends CRM_Contact_BAO_Query{

  /**
   * The various search modes
   *
   * @var int
   */
  CONST
    MODE_BOOKING = 32768;

  /**
   * class constructor which also does all the work
   *
   * @param array   $params
   * @param array   $returnProperties
   * @param array   $fields
   * @param boolean $includeContactIds
   * @param boolean $strict
   * @param boolean $mode - mode the search is operating on
   *
   * @return Object
   * @access public
   */
  function __construct(
    $params = NULL, $returnProperties = NULL, $fields = NULL,
    $includeContactIds = FALSE, $strict = FALSE, $mode = 1,
    $skipPermission = FALSE, $searchDescendentGroups = TRUE,
    $smartGroupCache = TRUE, $displayRelationshipType = NULL,
    $operator = 'AND'
  ) {
    /*
    parent::__construct($params,
     $returnProperties,
     $fields,
     $includeContactIds,
     $strict,
     $mode,
     $skipPermission,
     $searchDescendentGroups,
     $smartGroupCache ,
     $displayRelationshipType,
     $operator);*/


    $this->_params = &$params;
    if ($this->_params == NULL) {
      $this->_params = array();
    }
    if (empty($returnProperties)) {
      $this->_returnProperties = self::defaultReturnProperties($mode);
    }
    else {
      $this->_returnProperties = &$returnProperties;
    }

    $this->_includeContactIds = $includeContactIds;
    $this->_strict = $strict;
    $this->_mode = $mode;
    $this->_skipPermission = $skipPermission;
    $this->_smartGroupCache = $smartGroupCache;
    $this->_displayRelationshipType = $displayRelationshipType;
    $this->setOperator($operator);

    if ($fields) {
      $this->_fields = &$fields;
      $this->_search = FALSE;
      $this->_skipPermission = TRUE;
    }
    else {
      $this->_fields = CRM_Contact_BAO_Contact::exportableFields('Individual', FALSE, TRUE, TRUE);
      $fields = CRM_Core_Component::getQueryFields();

      unset($fields['note']);
      $this->_fields = array_merge($this->_fields, $fields);

      // add booking fields
      $fields = CRM_Booking_BAO_Booking::exportableFields();
      $this->_fields = array_merge($this->_fields, $fields);

      // add any fields provided by hook implementers
      $extFields = CRM_Contact_BAO_Query_Hook::singleton()->getFields();
      $this->_fields = array_merge($this->_fields, $extFields);
    }

    // basically do all the work once, and then reuse it
    $this->initialize();

  }

  /**
   * function which actually does all the work for the constructor
   *
   * @return void
   * @access private
   */
  function initialize() {
    $this->_select = array();
    $this->_element = array();
    $this->_tables = array();
    $this->_whereTables = array();
    $this->_where = array();
    $this->_qill = array();
    $this->_options = array();
    $this->_cfIDs = array();
    $this->_paramLookup = array();
    $this->_having = array();

    $this->_customQuery = NULL;

    $this->_select['contact_id'] = 'contact_a.id as contact_id';
    $this->_element['contact_id'] = 1;
    $this->_tables['civicrm_contact'] = 1;


    if (!empty($this->_params)) {
      $this->buildParamsLookup();
    }

    $this->_whereTables = $this->_tables;

    $this->selectClause();
    $this->_whereClause = $this->whereClause();

    $this->_fromClause = self::fromClause($this->_tables, NULL, NULL, $this->_primaryLocation, $this->_mode);

    $this->_simpleFromClause = self::fromClause($this->_whereTables, NULL, NULL, $this->_primaryLocation, $this->_mode);

    $this->openedSearchPanes(TRUE);

  }


  /**
   * Given a list of conditions in params and a list of desired
   * return Properties generate the required select and from
   * clauses. Note that since the where clause introduces new
   * tables, the initial attempt also retrieves all variables used
   * in the params list
   *
   * @return void
   * @access public
   */
  function selectClause() {


    $this->addSpecialFields();

    CRM_Booking_BAO_Query::select($this);

    // add location as hierarchical elements
    $this->addHierarchicalElements();

    // add multiple field like website
    $this->addMultipleElements();

    //fix for CRM-951
    //CRM_Core_Component::alterQuery($this, 'select');

    //CRM_Contact_BAO_Query_Hook::singleton()->alterSearchQuery($this, 'select');

    /*
    if (!empty($this->_cfIDs)) {
      $this->_customQuery = new CRM_Core_BAO_CustomQuery($this->_cfIDs, TRUE);
      $this->_customQuery->query();
      $this->_select = array_merge($this->_select, $this->_customQuery->_select);
      $this->_element = array_merge($this->_element, $this->_customQuery->_element);
      $this->_tables = array_merge($this->_tables, $this->_customQuery->_tables);
      $this->_whereTables = array_merge($this->_whereTables, $this->_customQuery->_whereTables);
      $this->_options = $this->_customQuery->_options;
    }*/
  }


  /**
   * create the from clause
   *
   * @param array $tables tables that need to be included in this from clause
   *                      if null, return mimimal from clause (i.e. civicrm_contact)
   * @param array $inner  tables that should be inner-joined
   * @param array $right  tables that should be right-joined
   *
   * @return string the from clause
   * @access public
   * @static
   */
  static function fromClause(&$tables, $inner = NULL, $right = NULL, $primaryLocation = TRUE, $mode = 1) {


   $from = ' FROM civicrm_contact contact_a';
    if (empty($tables)) {
      return $from;
    }

     //format the table list according to the weight
    $info = CRM_Core_TableHierarchy::info();
    $lastVal = array_pop($info);
    $info["civicrm_booking"] = $lastVal + 1;

    foreach ($tables as $key => $value) {
      $k = 99;
      if (strpos($key, '-') !== FALSE) {
        $keyArray = explode('-', $key);
        $k = CRM_Utils_Array::value('civicrm_' . $keyArray[1], $info, 99);
      }
      elseif (strpos($key, '_') !== FALSE) {
        $keyArray = explode('_', $key);
        if (is_numeric(array_pop($keyArray))) {
          $k = CRM_Utils_Array::value(implode('_', $keyArray), $info, 99);
        }
        else {
          $k = CRM_Utils_Array::value($key, $info, 99);
        }
      }
      else {
        $k = CRM_Utils_Array::value($key, $info, 99);
      }
      $tempTable[$k . ".$key"] = $key;
    }
    ksort($tempTable);
    $newTables = array();
    foreach ($tempTable as $key) {
      $newTables[$key] = $tables[$key];
    }

    $tables = $newTables;
    foreach ($tables as $name => $value) {
      if (!$value) {
        continue;
      }

      if (CRM_Utils_Array::value($name, $inner)) {
        $side = 'INNER';
      }
      elseif (CRM_Utils_Array::value($name, $right)) {
        $side = 'RIGHT';
      }
      else {
        $side = 'LEFT';
      }

      if ($value != 1) {
        // if there is already a join statement in value, use value itself
        if (strpos($value, 'JOIN')) {
          $from .= " $value ";
        }
        else {
          $from .= " $side JOIN $name ON ( $value ) ";
        }
        continue;
      }
      switch ($name) {
        case 'civicrm_booking':
        case 'booking_status':
        case 'booking_payment_status':
          $from .= CRM_Booking_BAO_Query::from($name, $mode, $side);
          continue;
        default:
          $from .= CRM_Core_Component::from($name, $mode, $side);
          $from .= CRM_Contact_BAO_Query_Hook::singleton()->buildSearchfrom($name, $mode, $side);
          continue;
      }
    }
    return $from;
  }





  /**
   * generate the query based on what type of query we need
   *
   * @param boolean $count
   * @param boolean $sortByChar
   * @param boolean $groupContacts
   *
   * @return the sql string for that query (this will most likely
   * change soon)
   * @access public
   */
  function query($count = FALSE, $sortByChar = FALSE, $groupContacts = FALSE) {

    if ($count) {
      if (isset($this->_distinctComponentClause)) {
        // we add distinct to get the right count for components
        // for the more complex result set, we use GROUP BY the same id
        // CRM-9630
        $select = "SELECT count( DISTINCT {$this->_distinctComponentClause} )";
      }
      else {
        $select = 'SELECT count(DISTINCT contact_a.id) as rowCount';
      }
      $from = $this->_simpleFromClause;
      if ($this->_useDistinct) {
        $this->_useGroupBy = TRUE;
      }
    }
    elseif ($sortByChar) {
      $select = 'SELECT DISTINCT UPPER(LEFT(contact_a.sort_name, 1)) as sort_name';
      $from = $this->_simpleFromClause;
    }
    elseif ($groupContacts) {
      $select = 'SELECT contact_a.id as id';
      if ($this->_useDistinct) {
        $this->_useGroupBy = TRUE;
      }
      $from = $this->_simpleFromClause;
    }
    else {
      if (CRM_Utils_Array::value('group', $this->_paramLookup)) {
        // make sure there is only one element
        // this is used when we are running under smog and need to know
        // how the contact was added (CRM-1203)
        if ((count($this->_paramLookup['group']) == 1) &&
          (count($this->_paramLookup['group'][0][2]) == 1)
        ) {
          $groups = array_keys($this->_paramLookup['group'][0][2]);
          $groupId = $groups[0];

          //check if group is saved search
          $group = new CRM_Contact_BAO_Group();
          $group->id = $groupId;
          $group->find(TRUE);

          if (!isset($group->saved_search_id)) {
            $tbName = "`civicrm_group_contact-{$groupId}`";
            $this->_select['group_contact_id'] = "$tbName.id as group_contact_id";
            $this->_element['group_contact_id'] = 1;
            $this->_select['status'] = "$tbName.status as status";
            $this->_element['status'] = 1;
          }
        }
        $this->_useGroupBy = TRUE;
      }
      if ($this->_useDistinct && !isset($this->_distinctComponentClause)) {
        if (!($this->_mode & CRM_Contact_BAO_Query::MODE_ACTIVITY)) {
          // CRM-5954
          $this->_select['contact_id'] = 'contact_a.id as contact_id';
          $this->_useDistinct = FALSE;
          $this->_useGroupBy = TRUE;
        }
      }

      $select = "SELECT ";
      if (isset($this->_distinctComponentClause)) {
        $select .= "{$this->_distinctComponentClause}, ";
      }
      $select .= implode(', ', $this->_select);
      $from = $this->_fromClause;
    }

    $where = '';
    if (!empty($this->_whereClause)) {
      $where = "WHERE {$this->_whereClause}";
    }

    $having = '';
    if (!empty($this->_having)) {
      foreach ($this->_having as $havingsets) {
        foreach ($havingsets as $havingset) {
          $havingvalue[] = $havingset;
        }
      }
      $having = ' HAVING ' . implode(' AND ', $havingvalue);
    }

    // if we are doing a transform, do it here
    // use the $from, $where and $having to get the contact ID
    if ($this->_displayRelationshipType) {
      $this->filterRelatedContacts($from, $where, $having);
    }

    return array($select, $from, $where, $having);
  }

  function &getWhereValues($name, $grouping) {

    $result = NULL;
    foreach ($this->_params as $values) {
      if ($values[0] == $name && $values[3] == $grouping) {
        return $values;
      }
    }

    return $result;
  }


  function whereClauseSingle(&$values) {
    switch ($values[0]) {
      case 'booking_status_id':
      case 'booking_payment_id':
        CRM_Booking_BAO_Query::whereClauseSingle($values, $this);
        return;
      default:
        $this->restWhere($values);
        return;
    }

  }

  /**
   * Given a list of conditions in params generate the required
   * where clause
   *
   * @return void
   * @access public
   */
  function whereClause() {

    $this->_where[0] = array();
    $this->_qill[0] = array();


    $this->includeContactIds();
    if (!empty($this->_params)) {
      foreach (array_keys($this->_params) as $id) {
        if (!CRM_Utils_Array::value(0, $this->_params[$id])) {
          continue;
        }
        // check for both id and contact_id
        if ($this->_params[$id][0] == 'id' || $this->_params[$id][0] == 'contact_id') {
          if (
            $this->_params[$id][1] == 'IS NULL' ||
            $this->_params[$id][1] == 'IS NOT NULL'
          ) {
            $this->_where[0][] = "contact_a.id {$this->_params[$id][1]}";
          }
          elseif (is_array($this->_params[$id][2])) {
            $idList = implode("','", $this->_params[$id][2]);
            //why on earth do they put ' in the middle & not on the outside? We have to assume it's
            //to support 'something' so lets add them conditionally to support the api (which is a tested flow
            // so if you are looking to alter this check api test results
            if(strpos(trim($idList), "'") > 0) {
              $idList = "'" . $idList . "'";
            }

            $this->_where[0][] = "contact_a.id IN ({$idList})";
          }
          else {
            $this->_where[0][] = "contact_a.id {$this->_params[$id][1]} {$this->_params[$id][2]}";
          }
        }
        else {
          $this->whereClauseSingle($this->_params[$id]);
        }
      }

      CRM_Core_Component::alterQuery($this, 'where');

      CRM_Contact_BAO_Query_Hook::singleton()->alterSearchQuery($this, 'where');
    }

    if ($this->_customQuery) {
      // Added following if condition to avoid the wrong value diplay for 'myaccount' / any UF info.
      // Hope it wont affect the other part of civicrm.. if it does please remove it.
      if (!empty($this->_customQuery->_where)) {
        $this->_where = CRM_Utils_Array::crmArrayMerge($this->_where, $this->_customQuery->_where);
      }

      $this->_qill = CRM_Utils_Array::crmArrayMerge($this->_qill, $this->_customQuery->_qill);
    }

    $clauses = array();
    $andClauses = array();

    $validClauses = 0;
    if (!empty($this->_where)) {
      foreach ($this->_where as $grouping => $values) {
        if ($grouping > 0 && !empty($values)) {
          $clauses[$grouping] = ' ( ' . implode(" {$this->_operator} ", $values) . ' ) ';
          $validClauses++;
        }
      }

      if (!empty($this->_where[0])) {
        $andClauses[] = ' ( ' . implode(" {$this->_operator} ", $this->_where[0]) . ' ) ';
      }
      if (!empty($clauses)) {
        $andClauses[] = ' ( ' . implode(' OR ', $clauses) . ' ) ';
      }

      if ($validClauses > 1) {
        $this->_useDistinct = TRUE;
      }
    }

    return implode(' AND ', $andClauses);
  }


   /**
   * create and query the db for an contact search
   *
   * @param int      $offset   the offset for the query
   * @param int      $rowCount the number of rows to return
   * @param string   $sort     the order by string
   * @param boolean  $count    is this a count only query ?
   * @param boolean  $includeContactIds should we include contact ids?
   * @param boolean  $sortByChar if true returns the distinct array of first characters for search results
   * @param boolean  $groupContacts if true, return only the contact ids
   * @param boolean  $returnQuery   should we return the query as a string
   * @param string   $additionalWhereClause if the caller wants to further restrict the search (used for components)
   * @param string   $additionalFromClause should be clause with proper joins, effective to reduce where clause load.
   *
   * @return CRM_Contact_DAO_Contact
   * @access public
   */
  function searchQuery(
    $offset = 0, $rowCount = 0, $sort = NULL,
    $count = FALSE, $includeContactIds = FALSE,
    $sortByChar = FALSE, $groupContacts = FALSE,
    $returnQuery = FALSE,
    $additionalWhereClause = NULL, $sortOrder = NULL,
    $additionalFromClause = NULL, $skipOrderAndLimit = FALSE
  ) {

    if ($includeContactIds) {
      $this->_includeContactIds = TRUE;
      $this->_whereClause = $this->whereClause();
    }

    $onlyDeleted = in_array(array('deleted_contacts', '=', '1', '0', '0'), $this->_params);

    // if we’re explicitly looking for a certain contact’s contribs, events, etc.
    // and that contact happens to be deleted, set $onlyDeleted to true
    foreach ($this->_params as $values) {
      $name = CRM_Utils_Array::value(0, $values);
      $op = CRM_Utils_Array::value(1, $values);
      $value = CRM_Utils_Array::value(2, $values);
      if ($name == 'contact_id' and $op == '=') {
        if (CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $value, 'is_deleted')) {
          $onlyDeleted = TRUE;
        }
        break;
      }
    }
    //TODO:: Fix permission clause
    $this->setSkipPermission(TRUE); //Hack skip permission so the query/count would work!
    $this->generatePermissionClause($onlyDeleted, $count);

    // building the query string
    $groupBy = NULL;
    if (!$count) {
      if (isset($this->_groupByComponentClause)) {
        $groupBy = $this->_groupByComponentClause;
      }
      elseif ($this->_useGroupBy) {
        $groupBy = ' GROUP BY contact_a.id';
      }
    }

    $order = $orderBy = $limit = '';
    if (!$count) {
      $config = CRM_Core_Config::singleton();
      if ($config->includeOrderByClause || isset($this->_distinctComponentClause)) {
        if ($sort) {
          if (is_string($sort)) {
            $orderBy = $sort;
          }
          else {
            $orderBy = trim($sort->orderBy());
          }
          if (!empty($orderBy)) {
            // this is special case while searching for
            // changelog CRM-1718
            if (preg_match('/sort_name/i', $orderBy)) {
              $orderBy = str_replace('sort_name', 'contact_a.sort_name', $orderBy);
            }

            $order = " ORDER BY $orderBy";

            if ($sortOrder) {
              $order .= " $sortOrder";
            }

            // always add contact_a.id to the ORDER clause
            // so the order is deterministic
            if (strpos('contact_a.id', $order) === FALSE) {
              $order .= ", contact_a.id";
            }
          }
        }
        elseif ($sortByChar) {
          $order = " ORDER BY UPPER(LEFT(contact_a.sort_name, 1)) asc";
        }
        else {
          $order = " ORDER BY contact_a.sort_name asc, contact_a.id";
        }
      }


      if ($rowCount > 0 && $offset >= 0) {
        $limit = " LIMIT $offset, $rowCount ";
      }
    }

    // note : this modifies _fromClause and _simpleFromClause
    $this->includePseudoFieldsJoin($sort);

    list($select, $from, $where, $having) = $this->query($count, $sortByChar, $groupContacts);

    if(!empty($this->_permissionWhereClause)){
      if (empty($where)) {
        $where = "WHERE $this->_permissionWhereClause";
      }
      else {
        $where = "$where AND $this->_permissionWhereClause";
      }
    }

    if ($additionalWhereClause) {
      $where = $where . ' AND ' . $additionalWhereClause;
    }

     //additional from clause should be w/ proper joins.
    if ($additionalFromClause) {
      $from .= "\n" . $additionalFromClause;
    }

    // if we are doing a transform, do it here
    // use the $from, $where and $having to get the contact ID
    if ($this->_displayRelationshipType) {
      $this->filterRelatedContacts($from, $where, $having);
    }

    if ($skipOrderAndLimit) {
      $query = "$select $from $where $having $groupBy";
    }
    else {
      $query = "$select $from $where $having $groupBy $order $limit";
    }

    if ($returnQuery) {
      return $query;
    }

    if ($count) {
      return CRM_Core_DAO::singleValueQuery($query);
    }

    $dao = CRM_Core_DAO::executeQuery($query);
    if ($groupContacts) {
      $ids = array();
      while ($dao->fetch()) {
        $ids[] = $dao->id;
      }
      return implode(',', $ids);
    }

    return $dao;
  }

}
