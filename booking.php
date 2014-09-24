<?php

require_once 'booking.civix.php';



/**
 * Implementation of hook_civicrm_tabs()
 *
 * Display a booking tab listing booking belong to that contact.
 */
function booking_civicrm_tabs(&$tabs, $cid) {
    $count = CRM_Booking_BAO_Booking::getBookingContactCount($cid); //TODO Count number of booking and show on the tab
    $tab = array(
      'id' => 'booking',
      'count' => $count,
      'title' => 'Bookings',
      'weight' => 0, //we are at first tab
    );
    $tab['url'] = CRM_Utils_System::url('civicrm/contact/view/booking', "reset=1&cid={$cid}&snippet=1&force=1", false, null, false);
    $tabs[] = $tab;

}


/**
 * Implementation of hook_civicrm_config
 */
function booking_civicrm_config(&$config) {
  _booking_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function booking_civicrm_xmlMenu(&$files) {
  _booking_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function booking_civicrm_install() {

	require_once 'CRM/Utils/Migrate/Import.php';
  $import = new CRM_Utils_Migrate_Import( );

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

  $op = $extRoot  . 'xml' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'OptionGroups.xml';

  $import->run( $op );

  return _booking_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function booking_civicrm_uninstall() {
  return _booking_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function booking_civicrm_enable() {
	// rebuild the menu so our path is picked up
  require_once 'CRM/Core/Invoke.php';
  CRM_Core_Invoke::rebuildMenuAndCaches( );
  return _booking_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function booking_civicrm_disable() {
  return _booking_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function booking_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _booking_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function booking_civicrm_managed(&$entities) {
  return _booking_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_queryObjects
 */
function booking_civicrm_queryObjects(&$queryObjects, $type) {
  if ($type == 'Contact') {
    $queryObjects[] = new CRM_Booking_BAO_Query();
  }
  elseif ($type == 'Report') {}
}

/**
 * Implementation of hook_civicrm_postProcess
 */
function booking_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
  if($objectName == 'Contribution'){
    if($op == 'delete'){
      CRM_Core_DAO::executeQuery("DELETE FROM civicrm_booking_payment WHERE contribution_id = $objectId");
    }
  }
}

/**
 * Implementation of hook_civicrm_entityTypes
 */
function booking_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = array(
    'name' => 'AdhocCharges',
    'class' => 'CRM_Booking_DAO_AdhocCharges',
    'table' => 'civicrm_booking_adhoc_charges',
  );
  $entityTypes[] = array(
    'name' => 'AdhocChargesItem',
    'class' => 'CRM_Booking_DAO_AdhocChargesItem',
    'table' => 'civicrm_booking_adhoc_charges_item',
  );
  $entityTypes[] = array(
    'name' => 'Booking',
    'class' => 'CRM_Booking_DAO_Booking',
    'table' => 'civicrm_booking',
  );
  $entityTypes[] = array(
    'name' => 'BookingPayment',
    'class' => 'CRM_Booking_DAO_Payment',
    'table' => 'civicrm_booking_payment',
  );
  $entityTypes[] = array(
    'name' => 'Resource',
    'class' => 'CRM_Booking_DAO_Resource',
    'table' => 'civicrm_booking_resource',
  );
  $entityTypes[] = array(
    'name' => 'ResourceConfigOption',
    'class' => 'CRM_Booking_DAO_ResourceConfigOption',
    'table' => 'civicrm_booking_resource_config_option',
  );
  $entityTypes[] = array(
    'name' => 'ResourceConfigSet',
    'class' => 'CRM_Booking_DAO_ResourceConfigSet',
    'table' => 'civicrm_booking_resource_config_set',
  );
   $entityTypes[] = array(
    'name' => 'Slot',
    'class' => 'CRM_Booking_DAO_Slot',
    'table' => 'civicrm_booking_slot',
  );
   $entityTypes[] = array(
    'name' => 'SubSlot',
    'class' => 'CRM_Booking_DAO_SubSlot',
    'table' => 'civicrm_booking_sub_slot',
  );
   $entityTypes[] = array(
    'name' => 'Cancellation',
    'class' => 'CRM_Booking_DAO_Cancellation',
    'table' => 'civicrm_booking_cancellation'
     );
}

/**
 * Implementation of hook_civicrm_merge
 */
function booking_civicrm_merge ( $type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL ){
if (!empty($mainId) && !empty($otherId) && $type == 'sqls'){

    $query1 = "
      UPDATE civicrm_booking
      SET primary_contact_id=$mainId
      WHERE primary_contact_id=$otherId;
      ";
    $query2 = "
      UPDATE civicrm_booking
      SET secondary_contact_id=$mainId
      WHERE secondary_contact_id=$otherId;
      ";

    require_once('CRM/Core/DAO.php');
    $dao = CRM_Core_DAO::executeQuery( $query1 );
	$dao = CRM_Core_DAO::executeQuery( $query2 );

  }
}

/**
 * Add navigation for booking under "Administer" menu
 *
 * @param $params associated array of navigation menus
 */
function booking_civicrm_navigationMenu( &$params ) {

   $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS));
   if($result['id']){
      $bookingStatusGid = $result['id'];
   }

   $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => CRM_Booking_Utils_Constants::OPTION_RESOURCE_TYPE));
   if($result['id']){
      $resourceTypeGid = $result['id'];
   }

   $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => CRM_Booking_Utils_Constants::OPTION_RESOURCE_LOCATION));
   if($result['id']){
      $resourceLocationGId = $result['id'];
   }

   $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => CRM_Booking_Utils_Constants::OPTION_RESOURCE_CRITERIA));

   if($result['id']){
      $resourceCriteriaGId = $result['id'];
   }

   $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => CRM_Booking_Utils_Constants::OPTION_SIZE_UNIT));
   if($result['id']){
      $sizeUnitGid = $result['id'];
   }

   $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => CRM_Booking_Utils_Constants::OPTION_CANCELLATION_CHARGES));
   if($result['id']){
      $cancellationChargesGid = $result['id'];
   }

  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');
  // skip adding menu if there is no administer menu
  if ($administerMenuId) {
    // get the maximum key under administer menu
    $maxAdminMenuKey = _getMenuKeyMax($params);
    $nextAdminMenuKey = $maxAdminMenuKey+1;
    $key = $nextAdminMenuKey;
    $params[$administerMenuId]['child'][$nextAdminMenuKey] =  array(
        'attributes' => array(
          'label' => ts('CiviBooking'),
          'name' => 'admin_booking',
          //'url' => '#',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => $administerMenuId,
          'navID' => $nextAdminMenuKey,
          'active' => 1
        ),
        'child' =>  array(
        $key++ => array(
          'attributes' => array(
            'label' => ts('Manage Resources'),
            'name' => 'manage_resources',
            'url' => 'civicrm/admin/resource&reset=1',
            'permission' => null,
            'operator' => null,
            'separator' => 0,
            'parentID' => $nextAdminMenuKey,
            'navID' => 2,
            'active' => 1
          ),
         'child' => null
        ),
        $key++ => array(
          'attributes' => array(
            'label' => ts('Resource Configuration Set'),
            'name' => 'resource_config_set',
            'url' => 'civicrm/admin/resource/config_set&reset=1',
            'permission' => null,
            'operator' => null,
            'separator' => 0,
            'parentID' =>  $nextAdminMenuKey,
            'navID' => 2,
            'active' => 1
          ),
         'child' => null
        ),
        $key++ => array(
          'attributes' => array(
            'label' => ts('Additional Charges Item'),
            'name' => 'adhoc_charges_item',
            'url' => 'civicrm/admin/adhoc_charges_item&reset=1',
            'permission' => null,
            'operator' => null,
            'separator' => 0,
            'parentID' =>  $nextAdminMenuKey,
            'navID' => 2,
            'active' => 1
          ),
         'child' => null
        ),
         $key++ => array(
            'attributes' => array(
              'label' => ts('Booking Status'),
              'name' => 'booking_status',
              'url' => 'civicrm/admin/optionValue?gid=' . $bookingStatusGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' =>  $nextAdminMenuKey,
              'navID' => 3,
              'active' => 1
            ),
           'child' => null
          ),
          $key++ => array(
            'attributes' => array(
              'label' => ts('Resource Type'),
              'name' => 'resource_type',
              'url' => 'civicrm/admin/optionValue?gid=' . $resourceTypeGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $nextAdminMenuKey,
              'navID' => 4,
              'active' => 1
              ),
            'child' => null
          ),
          $key++ => array(
            'attributes' => array(
              'label' => ts('Resource Criteria'),
              'name' => 'resource_criteria',
              'url' => 'civicrm/admin/optionValue?gid=' . $resourceCriteriaGId .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $nextAdminMenuKey,
              'navID' => 5,
              'active' => 1
            ),
            'child' => null
          ),
          $key++ => array(
            'attributes' => array(
              'label' => ts('Size Unit'),
              'name' => 'size_unit',
              'url' =>'civicrm/admin/optionValue?gid=' . $sizeUnitGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $nextAdminMenuKey,
              'navID' => 6,
              'active' => 1
            ),
            'child' => null
          ),
          $key++ => array(
            'attributes' => array(
              'label' => ts('Cancellation Charges'),
              'name' => 'cancellation_charges',
              'url' =>'civicrm/admin/optionValue?gid=' . $cancellationChargesGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $nextAdminMenuKey,
              'navID' => 6,
              'active' => 1
            ),
            'child' => null
          ),
          $key++ => array(
            'attributes' => array(
              'label' => ts('Booking Component Settings'),
              'name' => 'booking_component_settings',
              'url' =>'civicrm/admin/setting/preferences/booking?reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $newbookingKey,
              'navID' => 7,
              'active' => 1
            ),
            'child' => null
          ),
        ),
      );
   }

   $maxKey = ( max( array_keys($params) ) );
   
   $findBooking =  array(
        'attributes' => array(
          'label' => ts('Find Bookings'),
          'name' => 'find_booking',
          'url' => 'civicrm/booking/search&reset=1',
          'permission' => null,
          'operator' => null,
          'separator' => 0,
          'parentID' => null,
          'navID' => 3,
          'active' => 1
        ),
       'child' => null
      );

   $bookingMenu = array(
    'attributes' => array(
      'label' => ts('Booking'),
      'name' => 'booking',
      'url' => null,
      'permission' => null,
      'operator' => null,
      'separator' => null,
      'parentID' => null,
      'navID' => null,
      'active' => 1
    ),
    'child' => array(
      $key++ => array(
        'attributes' => array(
          'label' => ts('New Booking'),
          'name' => 'new_booking',
          'url' => 'civicrm/booking/add&reset=1',
          'permission' => null,
          'operator' => null,
          'separator' => 0,
          'parentID' => null,
          'navID' => 2 ,
          'active' => 1
        ),
      'child' => null
      ),
      $key++ => $findBooking,
      $key++ => array(
        'attributes' => array(
          'label' => ts('Day View'),
          'name' => 'day_view',
          'url' => 'civicrm/booking/day-view&reset=1',
          'permission' => null,
          'operator' => null,
          'separator' => 0,
          'parentID' => null,
          'navID' => 2 ,
          'active' => 1
        )
      ),
    )
  );
  array_push($params, $bookingMenu);
// array_unshift($params, $bookingMenu);
  /*
  $searchMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Search...', 'id', 'name');
  if ($searchMenuId) {
    $maxSearchMenuKey = max( array_keys($params[$searchMenuId]['child']));
    $nextSearchMenuKey = $maxSearchMenuKey + 1;
    $beforeMaxSearchMenuKey = $maxSearchMenuKey - 1;
    //remove lasted seperator for lasted Find
    $params[$searchMenuId]['child'][$beforeMaxSearchMenuKey]['attributes']['separator'] = 0;

    $lastSearchMenu = $params[$searchMenuId]['child'][$maxSearchMenuKey];
    unset($params[$searchMenuId]['child'][$maxSearchMenuKey]);
    $findBooking['attributes']['separator'] = 1;
    $params[$searchMenuId]['child'][$maxSearchMenuKey] = $findBooking;
    $params[$searchMenuId]['child'][$nextSearchMenuKey] = $lastSearchMenu;

    //move search menu to be at the first of the array
    $searchMenuTemp =  $params[$searchMenuId];
    unset($params[$searchMenuId]);
    array_unshift($params, $searchMenuTemp);
  }
  */

}

function _getMenuKeyMax($menuArray) {
  $max = array(max(array_keys($menuArray)));
  foreach($menuArray as $v) { 
    if (!empty($v['child'])) {
      $max[] = _getMenuKeyMax($v['child']); 
    }
  }
  return max($max);
}