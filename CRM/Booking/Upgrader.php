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
    $params = [
      'sequential' => 1,
      'label' =>  'Booking',
      'name' => CRM_Booking_Utils_Constants::ACTIVITY_TYPE,
    ];
    //chcck if it exist in case of re-installation
    $optionValue = civicrm_api3('OptionValue', 'get', $params);
    if ($optionValue['count'] == 0) {
      $params['weight'] = 1;
      $params['is_reserved'] = 1;
      $params['is_active'] = 1;
      civicrm_api3('ActivityType', 'create', $params);
    }

    //create new activity type for sending email confirmation :CVB-95
    $params = [
      'sequential' => 1,
      'label' =>  'Send booking confirmation',
      'name' => CRM_Booking_Utils_Constants::ACTIVITY_TYPE_SEND_EMAIL,
    ];
    //chcck if it exist in case of re-installation
    $optionValue = civicrm_api3('OptionValue', 'get', $params);
    if ($optionValue['count'] == 0) {
      $params['weight'] = 1;
      $params['is_reserved'] = 1;
      $params['is_active'] = 1;
      civicrm_api3('ActivityType', 'create', $params);
    }

    $result = civicrm_api3('OptionGroup', 'getsingle', [
        'sequential' => 1,
        'name' => 'msg_tpl_workflow_booking'
      ]
    );

    if (isset($result['id'])) {
      $params = [
        'sequential' => 1,
        'option_group_id' => $result['id'],
      ];
      $opvResult = civicrm_api3('OptionValue', 'get', $params);
      if (isset($opvResult['values'])  && !empty($opvResult['values'])) {
        foreach ($opvResult['values'] as $value) {
          switch ($value['name']) {
            case 'booking_offline_receipt':
              $html = file_get_contents($this->extensionDir . '/msg_tpl/booking_offline_receipt.html', FILE_USE_INCLUDE_PATH);
              $text = file_get_contents($this->extensionDir . '/msg_tpl/booking_offline_receipt.txt', FILE_USE_INCLUDE_PATH);
              $title = E::ts("Booking - Confirmation and Receipt (off-line)");
              break;
          }
          if (isset($title)) {
            $params = [
              'msg_title' => $title,
              'msg_subject' => E::ts("Booking - Confirmation Receipt") . ' - ' . E::ts("Booking Status:") . '{$booking_status}',
              'msg_text' => $text,
              'msg_html' => $html,
              'is_active' => 1,
              'workflow_id' =>  $value['id'],
              'is_default' => 1,
              'is_reserved' => 0,
            ];
            civicrm_api3('MessageTemplate', 'create', $params);
            $params['is_default'] = 0;
            $params['is_reserved'] = 1;
            //re-created another template
            civicrm_api3('MessageTemplate', 'create', $params);

          }
        }
      }
    }
    $this->executeSqlFile('sql/civibooking_default.sql');
    // Enable booking custom data
    $this->upgrade_1102();
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled
   */
  public function uninstall() {
  }

  /**
   * Example: Run a simple query when a module is enabled
   *
   */
  public function enable() {
    $this->executeSqlFile('sql/civibooking_enable.sql');
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
   */
  public function disable() {
    //TODO:: Disable the message template
    $this->executeSqlFile('sql/civibooking_disable.sql');
  }

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  public function upgrade_1100() {
    $this->ctx->log->info('Applying update 1100');
    if (!CRM_Core_BAO_SchemaHandler::checkIfFieldExists('civicrm_booking_config', 'unlimited_resource_time_config')) {
      $this->executeSqlFile('sql/upgrade_1100.sql');
    }
    return TRUE;
  }

  /**
   * This upgrade used to build navigation menus but is no longer required
   */
  public function upgrade_1101() {
    return TRUE;
  }

  public function upgrade_1102() {
    Civi::log()->info('Enabling Booking custom data');
    self::enableBookingCustomData();
    return TRUE;
  }

  public static function enableBookingCustomData() {
    // Enable Booking custom data
    $optionValue = [
      'name' => 'civicrm_booking',
      'label' => 'Booking',
      'value' => 'Booking',
    ];
    $optionValues = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => 'cg_extend_objects',
      'name' => $optionValue['name'],
    ]);
    if (!$optionValues['count']) {
      civicrm_api3('OptionValue', 'create', [
        'option_group_id' => 'cg_extend_objects',
        'name' => $optionValue['name'],
        'label' => $optionValue['label'],
        'value' => $optionValue['value'],
      ]);
    }
  }


  /**
   * Builds array with parameters to create menu items for the extension.
   *
   * @return array
   */
  public static function getMenuItems() {
    $bookingStatusGid = self::getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_BOOKING_STATUS);
    $resourceTypeGid = self::getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_RESOURCE_TYPE);
    $resourceLocationGId = self::getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_RESOURCE_LOCATION);
    $sizeUnitGid = self::getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_SIZE_UNIT);
    $cancellationChargesGid = self::getOptionGroupID(CRM_Booking_Utils_Constants::OPTION_CANCELLATION_CHARGES);

    $menus = [
      [
        'label' => ts('CiviBooking'),
        'name' => 'admin_booking',
        'url' => '#',
        'permission' => 'administer CiviBooking',
        'operator' => NULL,
        'separator' => 1,
        'parent_name' => 'Administer',
      ],
      [
        'label' => ts('Resource Configuration Set'),
        'name' => 'resource_config_set',
        'url' => CRM_Utils_System::url('civicrm/admin/resource/config_set', "reset=1", TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Manage Resources'),
        'name' => 'manage_resources',
        'url' => CRM_Utils_system::url('civicrm/admin/resource', "reset=1", TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Additional Charges Item'),
        'name' => 'adhoc_charges_item',
        'url' => CRM_Utils_system::url('civicrm/admin/adhoc_charges_item', "reset=1", TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Booking Status'),
        'name' => 'booking_status',
        'url' => CRM_Utils_system::url('civicrm/admin/options', ['gid' => $bookingStatusGid, 'reset' => 1], TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Resource Type'),
        'name' => 'resource_type',
        'url' => CRM_Utils_system::url('civicrm/admin/options', ['gid' => $resourceTypeGid, 'reset' => 1], TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Size Unit'),
        'name' => 'size_unit',
        'url' => CRM_Utils_system::url('civicrm/admin/options', ['gid' => $sizeUnitGid, 'reset' => 1], TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Cancellation Charges'),
        'name' => 'cancellation_charges',
        'url' => CRM_Utils_system::url('civicrm/admin/options', ['gid' => $cancellationChargesGid,'reset' => 1], TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Booking Component Settings'),
        'name' => 'booking_component_settings',
        'url' => CRM_Utils_system::url('civicrm/admin/setting/preferences/booking', "reset=1", TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Resource Location'),
        'name' => 'resource_location',
        'url' => CRM_Utils_system::url('civicrm/admin/options', ['gid' => $resourceLocationGId,'reset' => 1], TRUE),
        'permission' => NULL,
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'Administer/admin_booking',
      ],
      [
        'label' => ts('Find Bookings'),
        'name' => 'find_booking',
        'url' => CRM_Utils_system::url('civicrm/booking/search', "reset=1", TRUE),
        'permission' => 'administer CiviBooking,create and update bookings,view all bookings',
        'operator' => NULL,
        'separator' => 0,
      ],
      [
        'label' => ts('Booking'),
        'name' => 'booking',
        'url' => NULL,
        'permission' => 'administer CiviBooking,create and update bookings,view all bookings',
        'operator' => NULL,
        'separator' => NULL,
      ],
      [
        'label' => ts('New Booking'),
        'name' => 'new_booking',
        'url' => CRM_Utils_system::url('civicrm/booking/add', "reset=1", TRUE),
        'permission' => 'administer CiviBooking,create and update bookings',
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'booking',
      ],
      [
        'label' => ts('Day View'),
        'name' => 'day_view',
        'url' => CRM_Utils_system::url('civicrm/booking/day-view', "reset=1", TRUE),
        'permission' => 'administer CiviBooking,create and update bookings,view all bookings',
        'operator' => NULL,
        'separator' => 0,
        'parent_name' => 'booking',
      ],
    ];

    return $menus;
  }

  /**
   * Obtains an option group's ID given its name.
   *
   * @param string $name
   *
   * @return int
   */
  private static function getOptionGroupID($name) {
    try {
      return civicrm_api3('OptionGroup', 'getsingle', ['name' => $name])['id'];
    }
    catch (Exception $e) {
      return 0;
    }
  }

}
