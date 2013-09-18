UPDATE civicrm_option_value SET is_active = 0 WHERE option_group_id = 2 AND name = 'booking_activity_booking';

-- disable option groups related to civibooking
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'booking_booking_status';
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'booking_resource_type';
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'booking_resource_criteria';
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'booking_resource_location';
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'booking_cancellation_charges';
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'booking_size_unit';
UPDATE civicrm_option_group SET is_active = 0 WHERE name = 'msg_tpl_workflow_booking';
