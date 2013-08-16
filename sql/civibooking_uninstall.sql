SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_booking_payment`;
DROP TABLE IF EXISTS `civicrm_booking_slot`;
DROP TABLE IF EXISTS `civicrm_booking_resource_criteria`;
DROP TABLE IF EXISTS `civicrm_booking_resource_config_option`;
DROP TABLE IF EXISTS `civicrm_booking_resource`;
DROP TABLE IF EXISTS `civicrm_booking_resource_config_set`;
DROP TABLE IF EXISTS `civicrm_booking_cancellation`;
DROP TABLE IF EXISTS `civicrm_booking_config`;
DROP TABLE IF EXISTS `civicrm_booking`;

DELETE FROM civicrm_option_value WHERE option_group_id = 2 AND name = 'civibooking_acivity_booking';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'civibooking_booking_status' ;

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'civibooking_resource_type';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'civibooking_resource_criteria';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'civibooking_resource_location';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'civibooking_cancellation_charges';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'civibooking_size_unit';

--all option groups related to civibooking
DELETE FROM civicrm_option_group WHERE name = 'civibooking_booking_status';
DELETE FROM civicrm_option_group WHERE name = 'civibooking_resource_type';
DELETE FROM civicrm_option_group WHERE name = 'civibooking_resource_criteria';
DELETE FROM civicrm_option_group WHERE name = 'civibooking_resource_location';
DELETE FROM civicrm_option_group WHERE name = 'civibooking_cancellation_charges';
DELETE FROM civicrm_option_group WHERE name = 'civibooking_size_unit';


SET FOREIGN_KEY_CHECKS=1;
