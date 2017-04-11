ALTER TABLE  `civicrm_booking_resource_config_option` 
ADD `owner_id` int(10) unsigned 
DEFAULT NULL 
COMMENT 'Add an owner id for resources of type contact.'
AFTER `is_deleted`
