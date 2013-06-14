<?php

require_once 'civibooking.civix.php';



/**
 * Implementation of hook_civicrm_tabs()
 *
 * Display a booking tab listing booking belong to that contact.
 */
function civibooking_civicrm_tabs(&$tabs, $cid) {
    $count = 0; //TODO Count number of booking and show on the tab
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
function civibooking_civicrm_config(&$config) {
  _civibooking_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function civibooking_civicrm_xmlMenu(&$files) {
  _civibooking_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function civibooking_civicrm_install() {
	require_once 'CRM/Utils/Migrate/Import.php';
  $import = new CRM_Utils_Migrate_Import( );

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

  $op = $extRoot  . 'xml' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'OptionGroups.xml';

  $import->run( $op );

  return _civibooking_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function civibooking_civicrm_uninstall() {
  _civibooking_delete_option_group('booking_status');
  _civibooking_delete_option_group('resource_type');
  _civibooking_delete_option_group('resource_location');
  _civibooking_delete_option_group('resource_criteria');
  _civibooking_delete_option_group('size_unit');  
  return _civibooking_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function civibooking_civicrm_enable() {
	// rebuild the menu so our path is picked up
  require_once 'CRM/Core/Invoke.php';
  CRM_Core_Invoke::rebuildMenuAndCaches( );
  return _civibooking_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function civibooking_civicrm_disable() {
  return _civibooking_civix_civicrm_disable();
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
function civibooking_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civibooking_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function civibooking_civicrm_managed(&$entities) {
  return _civibooking_civix_civicrm_managed($entities);
}


/**
* Delete  option group 
*
* @param $name integer, option group name
*
*/
function _civibooking_delete_option_group($name){
  $getResult = civicrm_api('OptionGroup', 'getsingle', array(
      'version' => 3,
      'name' => $name,
  ));
  if($getResult['id']){
    $ovResult = civicrm_api('OptionValue', 'get', array(
            'version' => 3,
            'option_group_id' =>  $getResult['id'],
          ));
      if($ovResult['values']){
        foreach ($ovResult['values'] as $ov) {
          $delResult = civicrm_api('OptionValue', 'delete', array(
           'version' => 3,
           'id' => $ov['id'],
          ));
        }
       }
    CRM_Core_DAO::executeQuery('DELETE FROM civicrm_option_group WHERE id = ' . $getResult['id']);
  } 
}

/**
 * Add navigation for CiviBooking under "Administer" menu
 *
 * @param $params associated array of navigation menus
 */
function civibooking_civicrm_navigationMenu( &$params ) {

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'booking_status')
   );
   if($result['id']){
      $bookingStatusGid = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'resource_type')
   );
   if($result['id']){
      $resourceTypeGid = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'resource_location')
   );
   if($result['id']){
      $resourceLocationGId = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'resource_criteria')
   );
   
   if($result['id']){
      $resourceCriteriaGId = $result['id'];
   }

   $result = civicrm_api('OptionGroup', 'getsingle', array(
    'version' => 3,
    'sequential' => 1,
    'name' => 'size_unit')
   );
   if($result['id']){
      $sizeUnitGid = $result['id'];
   }

 
    //  Get the maximum key of $params
   $maxKey = (max(array_keys($params)));

  $bookingKey = $maxKey + 1;
  $dashboardKey = $bookingKey + 1;
  $newbookingKey = $bookingKey + 2;
  $findbookingKey = $bookingKey + 3;
  $manageResourcesKey = $bookingKey + 4;
  $diaryViewKey = $bookingKey + 5;
  $bookingStatusKey = $bookingKey + 6;
  $resourceTypeKey = $bookingKey + 7;
  $resourceLocationKey = $bookingKey + 8;
  $resourceCriteriaKey = $bookingKey + 9;
  $sizeunitKey = $bookingKey + 10;

  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');
  // skip adding menu if there is no administer menu
  if ($administerMenuId) {
    // get the maximum key under adminster menu
    $maxAdminMenuKey = max( array_keys($params[$administerMenuId]['child']));
    $params[$administerMenuId]['child'][$maxKey+1] =  array(
        'attributes' => array(
          'label' => 'CiviBooking',
          'name' => 'admin_civibooking',
          'url' => '#',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => $maxAdminMenuKey + 1,
          'navID' => $manageResourcesKey ,
          'active' => 1
        ),
        'child' =>  array( 
          $bookingStatusKey => array(
            'attributes' => array(
              'label' => 'Booking status',
              'name' => 'booking_status',
              'url' => 'civicrm/admin/optionValue?gid=' . $bookingStatusGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' =>  $maxAdminMenuKey + 1,
              'navID' => $bookingStatusKey ,
              'active' => 1
            ),
           'child' => null
          ),
          $resourceTypeKey => array(
            'attributes' => array(
              'label' => 'Resource type',
              'name' => 'resource_type',
              'url' => 'civicrm/admin/optionValue?gid=' . $resourceTypeGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $maxAdminMenuKey + 1,
              'navID' => $resourceTypeKey ,
              'active' => 1
              ),
            'child' => null
          ),
          $resourceCriteriaKey => array(
            'attributes' => array(
              'label' => 'Resource criteria',
              'name' => 'resource_criteria',
              'url' => 'civicrm/admin/optionValue?gid=' . $resourceCriteriaGId .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $maxAdminMenuKey + 1,
              'navID' => $resourceCriteriaKey ,
              'active' => 1
            ),
            'child' => null
          ),
          $sizeunitKey => array(
            'attributes' => array(
              'label' => 'Size Unit',
              'name' => 'size_unit',
              'url' =>'civicrm/admin/optionValue?gid=' . $sizeUnitGid .'&reset=1',
              'permission' => null,
              'operator' => null,
              'separator' => 0,
              'parentID' => $maxAdminMenuKey + 1,
              'navID' => $sizeunitKey ,
              'active' => 1
            ),
            'child' => null
          ),
        ),
      );
   }

   $params[$bookingKey] = array(
    'attributes' => array(
      'label' => 'Booking',
      'name' => 'booking',
      'url' => null,
      'permission' => null,
      'operator' => null,
      'separator' => null,
      'parentID' => null,
      'navID' => $bookingKey,
      'active' => 1
    ),
    'child' => array(
      $dashboardKey => array(
        'attributes' => array(
          'label' => 'Dashboard',
          'name' => 'booking_dashboard',
          'url' => 'civicrm/booking/dashboard',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => $bookingKey,
          'navID' => $dashboardKey,
          'active' => 1
        ),
        'child' => null
      ),
      $newbookingKey => array(
        'attributes' => array(
          'label' => 'New booking',
          'name' => 'new_booking',
          'url' => 'civicrm/booking/add',
          'permission' => null,
          'operator' => null,
          'separator' => 0,
          'parentID' => $bookingKey,
          'navID' => $newbookingKey ,
          'active' => 1
        ),
      'child' => null
      ),
      $findbookingKey => array(
        'attributes' => array(
          'label' => 'Find booking',
          'name' => 'find_booking',
          'url' => 'civicrm/booking/search',
          'permission' => null,
          'operator' => null,
          'separator' => 0,
          'parentID' => $bookingKey,
          'navID' => $findbookingKey,
          'active' => 1
        ),
       'child' => null
      ),
      $manageResourcesKey => array(
        'attributes' => array(
          'label' => 'Manage resources',
          'name' => 'manage_resources',
          'url' => 'civicrm/booking/resource',
          'permission' => null,
          'operator' => null,
          'separator' => 0,
          'parentID' => $bookingKey,
          'navID' => $findbookingKey,
          'active' => 1
        ),
       'child' => null
      ),
      $diaryViewKey => array(
        'attributes' => array(
          'label' => 'Diary view',
          'name' => 'diary_view',
          'url' => 'civicrm/booking/diary',
          'permission' => null,
          'operator' => null,
          'separator' => 1,
          'parentID' => $bookingKey,
          'navID' => $diaryViewKey ,
          'active' => 1
          ),
        'child' => null
      ),
    )
  );
}