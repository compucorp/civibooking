
UPDATE civicrm_option_value SET is_active = 1 WHERE option_group_id = 2 AND name = 'civibooking_activity_booking';

--enable all option groups related to civibooking
UPDATE civicrm_option_group SET is_active = 1 WHERE name = 'civibooking_booking_status';
UPDATE civicrm_option_group SET is_active = 1 WHERE name = 'civibooking_resource_type';
UPDATE civicrm_option_group SET is_active = 1 WHERE name = 'civibooking_resource_criteria';
UPDATE civicrm_option_group SET is_active = 1 WHERE name = 'civibooking_resource_location';
UPDATE civicrm_option_group SET is_active = 1 WHERE name = 'civibooking_cancellation_charges';
UPDATE civicrm_option_group SET is_active = 1 WHERE name = 'civibooking_size_unit';
