<?php
use CRM_Booking_ExtensionUtil as E; 

/**
 * Collection of upgrade steps
 */
class CRM_Booking_Upgrader extends CRM_Booking_Upgrader_Base {

  /**
   * Example: Run an external SQL script when the module is installed
   */
  public function install() {
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'label' =>  'Booking',
      'name' => CRM_Booking_Utils_Constants::ACTIVITY_TYPE,
    );
    //chcck if it exist in case of re-installation 
    $optionValue = civicrm_api3('OptionValue', 'get',$params);
    if($optionValue['count'] == 0){
      $params['weight'] = 1;
      $params['is_reserved'] = 1;
      $params['is_active'] = 1;
      $result = civicrm_api('ActivityType', 'create', $params);
    }

    //create new activity type for sending email confirmation :CVB-95
    $params = array(
      'version' => 3,
      'sequential' => 1,
      'label' =>  'Send booking confirmation',
      'name' => CRM_Booking_Utils_Constants::ACTIVITY_TYPE_SEND_EMAIL,
    );
    //chcck if it exist in case of re-installation 
    $optionValue = civicrm_api3('OptionValue', 'get',$params);
    if($optionValue['count'] == 0){
      $params['weight'] = 1;
      $params['is_reserved'] = 1;
      $params['is_active'] = 1;
      $result = civicrm_api('ActivityType', 'create', $params);
    }

    $result = civicrm_api('OptionGroup', 'getsingle', array(
      'version' => 3,
      'sequential' => 1,
      'name' => 'msg_tpl_workflow_booking')
    );

    if(isset($result['id'])){
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_id' => $result['id'],
      );
      $opvResult = civicrm_api('OptionValue', 'get', $params);
      if(isset($opvResult['values'])  && !empty($opvResult['values'])){
        foreach ($opvResult['values'] as  $value) {
          switch ($value['name']) {
            case 'booking_offline_receipt':
              $html = file_get_contents($this->extensionDir . '/msg_tpl/booking_offline_receipt.html', FILE_USE_INCLUDE_PATH);
              $text = file_get_contents($this->extensionDir . '/msg_tpl/booking_offline_receipt.txt', FILE_USE_INCLUDE_PATH);
              $title = E::ts("Booking - Confirmation and Receipt (off-line)");
              break;
          }
          if(isset($title)){
            $params = array(
              'version' => 3,
              'sequential' => 1,
              'msg_title' => $title,
              'msg_subject' => E::ts("Booking - Confirmation Receipt").' - '.ts("Booking Status:").'{$booking_status}',
              'msg_text' => $text,
              'msg_html' => $html,
              'is_active' => 1,
              'workflow_id' =>  $value['id'],
              'is_default' => 1,
              'is_reserved' => 0,
            );
            $result = civicrm_api('MessageTemplate', 'create', $params);
            $params['is_default'] = 0;
            $params['is_reserved'] = 1;
            //re-created another template
            $result = civicrm_api('MessageTemplate', 'create', $params);

          }
        }
      }
    }
    $this->executeSqlFile('sql/civibooking_default.sql');
    $this->upgrade_1101();
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled
   */
  public function uninstall() {
    $this->removeNavigationMenus();
  }

  /**
   * Removes menu items added by this extension.
   */
  private function removeNavigationMenus() {
    $menuItems = $this->buildBookingSubMenusParameters();

    foreach ($menuItems as $item) {
      $this->removeNav($item['name']);
    }

    CRM_Core_BAO_Navigation::resetNavigation();
  }

  /**
   * Example: Run a simple query when a module is enabled
   *
*/
  public function enable() {
   $this->executeSqlFile('sql/civibooking_enable.sql');
   $this->toggleIsActiveMenuItems(true);
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
  */
  public function disable() {
    //TODO:: Disable the message template
    $this->executeSqlFile('sql/civibooking_disable.sql');
    $this->toggleIsActiveMenuItems(false);
  }

  /**
   * Sets is_active parameter for menu items created by this extension.
   *
   * @param $isActive
   */
  private function toggleIsActiveMenuItems($isActive) {
    $sortedItems = array();
    $menuItems = $this->buildBookingSubMenusParameters();
    $isActive = (int) $isActive;
    foreach ($menuItems as $item) {
      $parent = CRM_Utils_Array::value('parent_name', $item, '_noparent_');
      $sortedItems[$parent][] = $item['name'];
    }

    foreach ($sortedItems as $parent => $items) {
      $params = array(
        'sequential' => 1,
        'name' => array('IN' => $items),
        'parent_id' => $parent,
        'api.Navigation.create' => array('id' => '$value.id', 'is_active' => $isActive),
      );

      if ($parent === '_noparent_') {
        $params['parent_id'] = array('IS NULL' => 1);
      }
      
      $versionNum = $this->versionSwitcher();
      if ($versionNum >= 470){
        civicrm_api3('Navigation', 'get', $params);
      }
      else {
        $items = implode("','", $items);
        $query = "UPDATE civicrm_navigation SET is_active = {$isActive} WHERE name IN ('{$items}')";
        CRM_Core_DAO::executeQuery($query);
      }
    }

    CRM_Core_BAO_Navigation::resetNavigation();
  }

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  public function upgrade_1100() {
    $this->ctx->log->info('Applying update 1100');
    $this->executeSqlFile('sql/update_1100.sql');
    return TRUE;
  }

  /**
   * This upgrade builds navigation menus for the extension.
   */
  public function upgrade_1101() {
    $administerMenuId = $this->getAdministerMenuID();

    // skip adding menu if there is no administer menu
    if ($administerMenuId) {
      $bookingSubMenus = $this->buildBookingSubMenusParameters();

      foreach ($bookingSubMenus as $menuItem) {
        $this->addNav($menuItem);
      }
    }

    CRM_Core_BAO_Navigation::resetNavigation();
    
    return TRUE;
  }

  /**
   * Builds array with parameters to create menu items for the extension.
   *
   * @return array
   */
  private function buildBookingSubMenusParameters() {
    $bookingStatusGid = $this->getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS);
    $resourceTypeGid = $this->getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_RESOURCE_TYPE);
    $resourceLocationGId = $this->getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_RESOURCE_LOCATION);
    $sizeUnitGid = $this->getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_SIZE_UNIT);
    $cancellationChargesGid = $this->getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_CANCELLATION_CHARGES);

    $menus = array(
      array(
        'label' => ts('CiviBooking'),
        'name' => 'admin_booking',
        'url' => '#',
        'permission' => 'administer CiviBooking',
        'operator' => null,
        'separator' => 1,
        'parent_name' => 'Administer',
      ),
      array(
        'label' => ts('Resource Configuration Set'),
        'name' => 'resource_config_set',
        'url' => CRM_Utils_System::url('civicrm/admin/resource/config_set', "reset=1", TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Manage Resources'),
        'name' => 'manage_resources',
        'url' => CRM_Utils_system::url('civicrm/admin/resource', "reset=1", TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Additional Charges Item'),
        'name' => 'adhoc_charges_item',
        'url' => CRM_Utils_system::url('civicrm/admin/adhoc_charges_item', "reset=1", TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Booking Status'),
        'name' => 'booking_status',
        'url' => CRM_Utils_system::url('civicrm/admin/options', array('gid' => $bookingStatusGid, 'reset' => 1), TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Resource Type'),
        'name' => 'resource_type',
        'url' => CRM_Utils_system::url('civicrm/admin/options', array('gid' => $resourceTypeGid, 'reset' => 1), TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Size Unit'),
        'name' => 'size_unit',
        'url' => CRM_Utils_system::url('civicrm/admin/options', array('gid' => $sizeUnitGid, 'reset' => 1), TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Cancellation Charges'),
        'name' => 'cancellation_charges',
        'url' => CRM_Utils_system::url('civicrm/admin/options', array('gid' => $cancellationChargesGid,'reset' => 1), TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Booking Component Settings'),
        'name' => 'booking_component_settings',
        'url' => CRM_Utils_system::url('civicrm/admin/setting/preferences/booking', "reset=1", TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Resource Location'),
        'name' => 'resource_location',
        'url' => CRM_Utils_system::url('civicrm/admin/options', array('gid' => $resourceLocationGId,'reset' => 1), TRUE),
        'permission' => null,
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'admin_booking',
      ),
      array(
        'label' => ts('Find Bookings'),
        'name' => 'find_booking',
        'url' => CRM_Utils_system::url('civicrm/booking/search', "reset=1", TRUE),
        'permission' => 'administer CiviBooking,create and update bookings,view all bookings',
        'operator' => null,
        'separator' => 0,
      ),
      array(
        'label' => ts('Booking'),
        'name' => 'booking',
        'url' => null,
        'permission' => 'administer CiviBooking,create and update bookings,view all bookings',
        'operator' => null,
        'separator' => null,
      ),
      array(
        'label' => ts('New Booking'),
        'name' => 'new_booking',
        'url' => CRM_Utils_system::url('civicrm/booking/add', "reset=1", TRUE),
        'permission' => 'administer CiviBooking,create and update bookings',
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'booking',
      ),
      array(
        'label' => ts('Day View'),
        'name' => 'day_view',
        'url' => CRM_Utils_system::url('civicrm/booking/day-view', "reset=1", TRUE),
        'permission' => 'administer CiviBooking,create and update bookings,view all bookings',
        'operator' => null,
        'separator' => 0,
        'parent_name' => 'booking',
      ),
    );

    return $menus;
  }

  /**
   * Obtains ID of Administer menu.
   *
   * @return null|string
   */
  private function getAdministerMenuID() {
    $domain_id = CRM_Core_Config::domainID();

    $administerMenuId = CRM_Core_DAO::singleValueQuery("
      SELECT id
       FROM civicrm_navigation
      WHERE name = 'Administer'
        AND domain_id = $domain_id
    ");

    return $administerMenuId;
  }

  /**
   * Obtains an option group's ID given its name.
   *
   * @param string $name
   *
   * @return int
   */
  private function getOptionGroupID($name) {
    $result = civicrm_api3('OptionGroup', 'getsingle', array('name' => $name));

    if($result['id']){
      return $result['id'];
    }

    return 0;
  }

  /**
   * Adds given menu item to CiviCRM navigation.
   *
   * @param array $menuItem
   */
  private function addNav($menuItem) {
    if (isset($menuItem['parent_name'])) {
      $menuItem['parent_id'] = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', $menuItem['parent_name'], 'id', 'name');
      unset($menuItem['parent_name']);
    }

    $menuItem['is_active'] = 1;

    CRM_Core_BAO_Navigation::add($menuItem);
  }

  /**
   * Removes navigation item identified by $name from CiviCRM navigation.
   *
   * @param string $name
   */
  private function removeNav($name) {
    $versionNum = $this->versionSwitcher();
    if ($versionNum >= 470){
      civicrm_api3('Navigation', 'get', array(
        'sequential' => 1,
        'name' => $name,
        'api.Navigation.delete' => array('id' => '$value.id'),
      ));
    }
    else {
      $query = "DELETE FROM civicrm_navigation WHERE name = '{$name}'";
      CRM_Core_DAO::executeQuery($query);
    }
  }
  
  /**
   * Get civicrm version.
   * 
   * @return $versionNum
   */
  private function versionSwitcher() {
    $version = CRM_Utils_System::version();
    preg_match('/[0-9]\.[0-9]\.[0-9]/', $version, $matches);
    $versionNum = str_replace(".","",array_pop($matches));
    
    return $versionNum;
  }

  /**
   * Example: Run a couple simple queries
   *
   * @return TRUE on success
   * @throws Exception
   *
  public function upgrade_4200() {
    $this->ctx->log->info('Applying update 4200');
    CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
    CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
    return TRUE;
  } // */


  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(E::ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(E::ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(E::ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = E::ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } // */

}
