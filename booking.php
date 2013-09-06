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
      'title' => 'Booking',
      'weight' => '998',
    );
    //if ($count > 0) {
    $tab['url'] = CRM_Utils_System::url('civicrm/booking/tab', "reset=1&cid={$cid}&snippet=1", false, null, false);
    //}
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
  /*
  _booking_delete_option_group('booking_status');
  _booking_delete_option_group('resource_type');
  _booking_delete_option_group('resource_location');
  _booking_delete_option_group('resource_criteria');
  _booking_delete_option_group('cancellation_charges');
  _booking_delete_option_group('size_unit');
  $ov = civicrm_api('OptionValue', 'getsingle', array(
      'version' => 3,
      'name' => 'booking',
  ));
  if($ov['id']){
    $delResult = civicrm_api('OptionValue', 'delete', array(
      'version' => 3,
      'id' => $ov['id'],
    ));
  }*/
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
 * Add navigation for booking under "Administer" menu
 *
 * @param $params associated array of navigation menus
 */
function booking_civicrm_navigationMenu( &$params ) {

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'booking_booking_status')
   );
   if($result['id']){
      $bookingStatusGid = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'booking_resource_type')
   );
   if($result['id']){
      $resourceTypeGid = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'booking_resource_location')
   );
   if($result['id']){
      $resourceLocationGId = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'booking_resource_criteria')
   );

   if($result['id']){
      $resourceCriteriaGId = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'booking_size_unit')
   );
   if($result['id']){
      $sizeUnitGid = $result['id'];
   }

  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');
  // skip adding menu if there is no administer menu
  if ($administerMenuId) {
    // get the maximum key under adminster menu
    $maxAdminMenuKey = max( array_keys($params[$administerMenuId]['child']));
    $nextAdminMenuKey = $maxAdminMenuKey+1;
    $params[$administerMenuId]['child'][$nextAdminMenuKey] =  array(
        'attributes' => array(
          'label' => 'CiviBooking',
          'name' => 'admin_booking',
          'url' => '#',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => $administerMenuId,
          'navID' => $nextAdminMenuKey,
          'active' => 1
        ),
        'child' =>  array(
        1 => array(
          'attributes' => array(
            'label' => 'Manage Resources',
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
        2 => array(
          'attributes' => array(
            'label' => 'Resource Configuration Set',
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
         3 => array(
            'attributes' => array(
              'label' => 'Booking Status',
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
          4 => array(
            'attributes' => array(
              'label' => 'Resource Type',
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
          5 => array(
            'attributes' => array(
              'label' => 'Resource Criteria',
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
          6 => array(
            'attributes' => array(
              'label' => 'Size Unit',
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
          7 => array(
            'attributes' => array(
              'label' => 'Booking Component Settings',
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

   $bookingMenu = array(
    'attributes' => array(
      'label' => 'Booking',
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
      1 => array(
        'attributes' => array(
          'label' => 'Dashboard',
          'name' => 'booking_dashboard',
          'url' => 'civicrm/booking/dashboard&reset=1',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => null,
          'navID' => 1,
          'active' => 1
        ),
        'child' => null
      ),
      2 => array(
        'attributes' => array(
          'label' => 'New Booking',
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
      3 => array(
        'attributes' => array(
          'label' => 'Find Booking',
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
      ),
      4 => array(
        'attributes' => array(
          'label' => 'Diary View',
          'name' => 'diary_view',
          'url' => 'civicrm/booking/diary&reset=1',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => null,
          'navID' => 4 ,
          'active' => 1
          ),
        'child' => null
      ),
    )
  );
   array_unshift($params, $bookingMenu);

}
