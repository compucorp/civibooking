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


SET FOREIGN_KEY_CHECKS=1;

-- /*******************************************************
-- *
-- * civicrm_booking
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `primary_contact_id` int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     `secondary_contact_id` int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     `po_number` varchar(255) NOT NULL   ,
     `status_id` int unsigned NOT NULL   ,
     `title` varchar(50) NOT NULL   ,
     `description` varchar(255)    ,
     `discount_amount` decimal(20,2)    ,
     `notes` text    ,
     `participants_estimate` varchar(255)    ,
     `participants_actual` varchar(255)    ,
     `payment_status` int unsigned    ,
     `is_deleted` tinyint    ,
     `created_by` datetime NOT NULL   ,
     `created_date` int unsigned NOT NULL   ,
     `updated_by` int unsigned NOT NULL   ,
     `updated_date` datetime NOT NULL
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_primary_contact_id FOREIGN KEY (`primary_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_secondary_contact_id FOREIGN KEY (`secondary_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_config
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_config` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `financial_type_default` int unsigned NOT NULL   ,
     `day_start_at` time NOT NULL   ,
     `log_confirmation_email` tinyint NOT NULL  DEFAULT 0 ,
     `selected_email_address` varchar(255)    ,
     `cc_email_address` varchar(255)    ,
     `bcc_email_address` varchar(255)    ,
     `slot_avaliable_colour` varchar(10)    ,
     `slot_unavaliable_colour` varchar(10)
,
    PRIMARY KEY ( `id` )



)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_cancellation
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_cancellation` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `booking_id` int unsigned    COMMENT 'FK to Booking',
     `cancellation_date` datetime NOT NULL   ,
     `additional_charge` decimal(20,2) NOT NULL   ,
     `comment` text
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_cancellation_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_resource_config_set
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource_config_set` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `title` varchar(255) NOT NULL   ,
     `weight` int unsigned NOT NULL   ,
     `is_active` tinyint NOT NULL   ,
     `is_deleted` tinyint
,
    PRIMARY KEY ( `id` )



)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;


-- /*******************************************************
-- *
-- * civicrm_booking_resource
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `set_id` int unsigned    COMMENT 'FK to resource configuration option set',
     `label` varchar(255)    ,
     `description` varchar(255)    ,
     `weight` int NOT NULL   ,
     `type_id` int unsigned    COMMENT 'The type associated with this resource. Implicit FK to option_value row in booking_resource_type option_group.',
     `location_id` int unsigned    COMMENT 'The location associated with this resource. Implicit FK to option_value row in booking_resource_location option_group.',
     `is_unlimited` tinyint NOT NULL   ,
     `is_active` tinyint NOT NULL   ,
     `is_deleted` tinyint
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_resource_set_id FOREIGN KEY (`set_id`) REFERENCES `civicrm_booking_resource_config_set`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_resource_config_option
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource_config_option` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `set_id` int unsigned NOT NULL   COMMENT 'Foreign key to the resource set for this option.',
     `label` varchar(255) NOT NULL   ,
     `price` decimal(20,2) NOT NULL   ,
     `max_size` varchar(255)    ,
     `unit_id` int unsigned    COMMENT 'The unit associated with this config option. Implicit FK to option_value row in booking_size_unit option_group.',
     `weight` int unsigned NOT NULL   ,
     `is_active` tinyint NOT NULL
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_resource_config_option_set_id FOREIGN KEY (`set_id`) REFERENCES `civicrm_booking_resource_config_set`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_resource_criteria
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource_criteria` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `resource_id` int unsigned NOT NULL   COMMENT 'Foreign key to the resoure for this resource criteria.',
     `criteria_id` varchar(512) NOT NULL   COMMENT 'Foreign key to the resource criteria option group.'
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_resource_criteria_resource_id FOREIGN KEY (`resource_id`) REFERENCES `civicrm_booking_resource`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_slot
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_slot` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `booking_id` int unsigned    COMMENT 'FK to Booking ID',
     `config_id` int unsigned    COMMENT 'FK to resource configuration option ID',
     `start` datetime NOT NULL   ,
     `end` datetime NOT NULL   ,
     `quantity` int NOT NULL   ,
     `note` text    ,
     `parent_id` int unsigned    ,
     `is_cancelled` tinyint    ,
     `is_deleted` tinyint
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_slot_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_slot_config_id FOREIGN KEY (`config_id`) REFERENCES `civicrm_booking_resource_config_option`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;


-- /*******************************************************
-- *
-- * civicrm_booking_payment
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_payment` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `booking_id` int unsigned NOT NULL   COMMENT 'Foreign key to the booking id for this payment.',
     `contribution_id` int unsigned NOT NULL   COMMENT 'Foreign key to the contribution for this payment.'
,
    PRIMARY KEY ( `id` )


,          CONSTRAINT FK_civicrm_booking_payment_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_payment_contribution_id FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;


