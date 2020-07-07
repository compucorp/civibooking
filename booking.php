<?php
use CRM_Booking_ExtensionUtil as E;

require_once 'booking.civix.php';

/**
 * Implementation of hook_civicrm_tabs()
 *
 * Display a booking tab listing booking belong to that contact.
 */
function booking_civicrm_tabs(&$tabs, $cid) {
  $count = CRM_Booking_BAO_Booking::getBookingContactCount($cid); //TODO Count number of booking and show on the tab
  $tab = [
    'id' => 'booking',
    'count' => $count,
    'title' => 'Bookings',
    'weight' => 0, //we are at first tab
  ];
  $tab['url'] = CRM_Utils_System::url('civicrm/contact/view/booking', "reset=1&cid={$cid}&snippet=1&force=1", false, null, false);
  $tabs[] = $tab;
}

/**
 * Implementation of hook_civicrm_config
 */
function booking_civicrm_config(&$config) {
  // enable use of number_format php function in smarty templates when
  // security on(on by default for emails)
  $smarty = CRM_Core_Smarty::singleton();
  $smarty->security_settings['MODIFIER_FUNCS'][] = "number_format";
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
  _booking_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function booking_civicrm_uninstall() {
  _booking_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function booking_civicrm_enable() {
  _booking_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function booking_civicrm_disable() {
  _booking_civix_civicrm_disable();
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
  _booking_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_queryObjects
 */
function booking_civicrm_queryObjects(&$queryObjects, $type) {
  if ($type == 'Contact') {
    $queryObjects[] = new CRM_Booking_BAO_Query();
  }
}

/**
 * Implementation of hook_civicrm_postProcess
 */
function booking_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Contribution') {
    if ($op == 'delete') {
      CRM_Core_DAO::executeQuery("DELETE FROM civicrm_booking_payment WHERE contribution_id = $objectId");
    }
  }
}

/**
 * Implementation of hook_civicrm_entityTypes
 */
function booking_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = [
    'name' => 'AdhocCharges',
    'class' => 'CRM_Booking_DAO_AdhocCharges',
    'table' => 'civicrm_booking_adhoc_charges',
  ];
  $entityTypes[] = [
    'name' => 'AdhocChargesItem',
    'class' => 'CRM_Booking_DAO_AdhocChargesItem',
    'table' => 'civicrm_booking_adhoc_charges_item',
  ];
  $entityTypes[] = [
    'name' => 'Booking',
    'class' => 'CRM_Booking_DAO_Booking',
    'table' => 'civicrm_booking',
  ];
  $entityTypes[] = [
    'name' => 'BookingPayment',
    'class' => 'CRM_Booking_DAO_Payment',
    'table' => 'civicrm_booking_payment',
  ];
  $entityTypes[] = [
    'name' => 'Resource',
    'class' => 'CRM_Booking_DAO_Resource',
    'table' => 'civicrm_booking_resource',
  ];
  $entityTypes[] = [
    'name' => 'ResourceConfigOption',
    'class' => 'CRM_Booking_DAO_ResourceConfigOption',
    'table' => 'civicrm_booking_resource_config_option',
  ];
  $entityTypes[] = [
    'name' => 'ResourceConfigSet',
    'class' => 'CRM_Booking_DAO_ResourceConfigSet',
    'table' => 'civicrm_booking_resource_config_set',
  ];
  $entityTypes[] = [
    'name' => 'Slot',
    'class' => 'CRM_Booking_DAO_Slot',
    'table' => 'civicrm_booking_slot',
  ];
  $entityTypes[] = [
    'name' => 'SubSlot',
    'class' => 'CRM_Booking_DAO_SubSlot',
    'table' => 'civicrm_booking_sub_slot',
  ];
  $entityTypes[] = [
    'name' => 'Cancellation',
    'class' => 'CRM_Booking_DAO_Cancellation',
    'table' => 'civicrm_booking_cancellation'
  ];
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 */
function booking_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _booking_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_merge
 */
function booking_civicrm_merge ($type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL) {
  if (!empty($mainId) && !empty($otherId) && $type == 'sqls') {
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

    $dao = CRM_Core_DAO::executeQuery($query1);
    $dao = CRM_Core_DAO::executeQuery($query2);

  }
}

function booking_civicrm_permission(&$permissions) {
  $prefix = E::ts('CiviBooking') . ': ';
  $permissions['administer CiviBooking'] = $prefix . E::ts('administer CiviBooking');
  $permissions['create and update bookings'] = $prefix . E::ts('create and update bookings');
  $permissions['view all bookings'] = $prefix . E::ts('view all bookings');
}

/*
 * Implements hook_civicrm_alterAPIPermissions
 * @see function _civicrm_api3_permissions for mentioned uppercase issue
 */
function booking_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $commonBookingAPIPermissions = [
    'create' => [
      'administer CiviBooking',
    ],
    'delete' => [
      'administer CiviBooking',
    ],
    'get' => [
      [
        'administer CiviBooking',
        'create and update bookings',
        'view all bookings',
      ]
    ],
    'update' => [
      'administer CiviBooking',
    ],
  ];

  $bookingEntities = [
    'BookingPayment',
    'Booking',
    'Cancellation',
    'Slot',
    'SubSlot'
  ];

  $configEntities = [
    'AdhocChargesItem',
    'AdhocCharges',
    'ResourceConfigOption',
    'ResourceConfigSet',
    'Resource',
  ];

  // set common permissions
  foreach (array_merge($bookingEntities, $configEntities) as $entityName) {
    // permissions implementation needs lowercase entities
    $permissions[_civicrm_api_get_entity_name_from_camel($entityName)] = $commonBookingAPIPermissions;
  }

  //add custom permissions for create/update role
  foreach ($bookingEntities as $entityName) {
    $permissionArray = [['administer CiviBooking', 'create and update bookings']];
    // permissions implementation needs lowercase entities
    $entityName = _civicrm_api_get_entity_name_from_camel($entityName);
    $permissions[$entityName]['create'] = $permissionArray;
    $permissions[$entityName]['update'] = $permissionArray;
  }

}

/**
 * Implements hook_civicrm_apiWrappers()
 *
 * @param array $wrappers
 * @param array $apiRequest
 */
function booking_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if ($apiRequest['entity'] == 'Resource' && $apiRequest['action'] == 'get') {
    $wrappers[] = new CRM_Booking_APIWrapper();
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 */
function booking_civicrm_navigationMenu(&$menu) {
  $menuItems = CRM_Booking_Upgrader::getMenuItems();
  foreach ($menuItems as $item) {
    _booking_civix_insert_navigation_menu($menu, $item['parent_name'], $item);
  }
  _booking_civix_navigationMenu($menu);
}
