SET FOREIGN_KEY_CHECKS=0;

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'booking_status' ;

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'booking_resource_type';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'booking_resource_criteria';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'booking_resource_location';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'booking_cancellation_charges';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'booking_size_unit';

DELETE civicrm_msg_template FROM civicrm_msg_template
INNER JOIN civicrm_option_value ON civicrm_option_value.id = civicrm_msg_template.workflow_id
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'msg_tpl_workflow_booking';

DELETE civicrm_option_value FROM civicrm_option_value
INNER JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
WHERE civicrm_option_group.name = 'msg_tpl_workflow_booking';


--all option groups related to civibooking
DELETE FROM civicrm_option_group WHERE name = 'booking_status';
DELETE FROM civicrm_option_group WHERE name = 'booking_resource_type';
DELETE FROM civicrm_option_group WHERE name = 'booking_resource_criteria';
DELETE FROM civicrm_option_group WHERE name = 'booking_resource_location';
DELETE FROM civicrm_option_group WHERE name = 'booking_cancellation_charges';
DELETE FROM civicrm_option_group WHERE name = 'booking_size_unit';
DELETE FROM civicrm_option_group WHERE name = 'msg_tpl_workflow_booking';

SET FOREIGN_KEY_CHECKS=1;
