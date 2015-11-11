SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_booking_payment`;
DROP TABLE IF EXISTS `civicrm_booking_sub_slot`;
DROP TABLE IF EXISTS `civicrm_booking_slot`;
DROP TABLE IF EXISTS `civicrm_booking_resource_criteria`;
DROP TABLE IF EXISTS `civicrm_booking_resource_config_option`;
DROP TABLE IF EXISTS `civicrm_booking_resource`;
DROP TABLE IF EXISTS `civicrm_booking_adhoc_charges`;
DROP TABLE IF EXISTS `civicrm_booking_resource_config_set`;
DROP TABLE IF EXISTS `civicrm_booking_cancellation`;
DROP TABLE IF EXISTS `civicrm_booking_config`;
DROP TABLE IF EXISTS `civicrm_booking`;
DROP TABLE IF EXISTS `civicrm_booking_adhoc_charges_item`;


SET FOREIGN_KEY_CHECKS=1;


-- /*******************************************************
-- *
-- * civicrm_booking_adhoc_charges_item
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_adhoc_charges_item` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `name` varchar(255) NOT NULL   ,
     `label` varchar(255) NOT NULL   ,
     `price` decimal(20,2) NOT NULL   ,
     `weight` int unsigned NOT NULL   ,
     `is_active` tinyint   DEFAULT 1 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_active`(
        is_active
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )


)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `primary_contact_id` int unsigned NOT NULL   COMMENT 'FK to Contact ID',
     `secondary_contact_id` int unsigned NULL   COMMENT 'FK to Contact ID',
     `title` varchar(255) NOT NULL   ,
     `status_id` int unsigned NOT NULL   COMMENT 'The status associated with this booking. Implicit FK to option_value row in booking status option_group.',
     `booking_date` datetime NOT NULL   ,
     `start_date` datetime NOT NULL   ,
     `end_date` datetime NOT NULL   ,
     `po_number` varchar(255) NOT NULL   ,
     `total_amount` decimal(20,2) NOT NULL   COMMENT 'Total amount of this booking calculated from slots,sub slots, ad-hoc charges and discount amount',
     `description` varchar(255)    ,
     `note` text    ,
     `adhoc_charges_note` text    ,
     `participants_estimate` varchar(255)    ,
     `participants_actual` varchar(255)    ,
     `discount_amount` decimal(20,2)    ,
     `is_deleted` tinyint   DEFAULT 0 ,
     `created_by` int unsigned NOT NULL   ,
     `created_date` datetime NOT NULL   ,
     `updated_by` int unsigned NOT NULL   ,
     `updated_date` datetime NOT NULL
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_deleted`(
        is_deleted
  )

,          CONSTRAINT FK_civicrm_booking_primary_contact_id FOREIGN KEY (`primary_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_secondary_contact_id FOREIGN KEY (`secondary_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_config
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_config` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `domain_id` int unsigned    ,
     `day_start_at` time NOT NULL   ,
     `day_end_at` time NOT NULL   ,
     `time_period` int NOT NULL   ,
     `log_confirmation_email` tinyint NOT NULL  DEFAULT 0 COMMENT 'Create an activity record againt contact for conformation emails',
     `unlimited_resource_time_config` tinyint NOT NULL  DEFAULT 1 COMMENT 'Only allow unlimited resources to be booked within time span of the parent limited resource booking',
     `cc_email_address` varchar(255)    ,
     `bcc_email_address` varchar(255)    ,
     `slot_new_colour` varchar(10)    ,
     `slot_being_edited_colour` varchar(10)    ,
     `slot_booked_colour` varchar(10)    ,
     `slot_provisional_colour` varchar(10)    ,
     `slot_unavailable_colour` varchar(10)
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
     `cancellation_fee` decimal(20,2) NOT NULL   ,
     `additional_fee` decimal(20,2)    ,
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
     `is_active` tinyint   DEFAULT 1 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_active`(
        is_active
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )


)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;


-- /*******************************************************
-- *
-- * civicrm_booking_adhoc_charges
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_adhoc_charges` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `booking_id` int unsigned NOT NULL   COMMENT 'FK to Booking ID',
     `item_id` int unsigned NOT NULL   COMMENT 'FK to Item ID',
     `quantity` int NOT NULL   ,
     `is_cancelled` tinyint   DEFAULT 0 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_cancelled`(
        is_cancelled
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )

,          CONSTRAINT FK_civicrm_booking_adhoc_charges_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_adhoc_charges_item_id FOREIGN KEY (`item_id`) REFERENCES `civicrm_booking_adhoc_charges_item`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_resource
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `set_id` int unsigned    COMMENT 'FK to resource configuration option set',
     `label` varchar(255) NOT NULL   ,
     `description` varchar(255)    ,
     `type_id` varchar(512) NOT NULL   COMMENT 'The type associated with this resource. Implicit FK to option_value row in booking_resource_type option_group.',
     `location_id` varchar(512)    COMMENT 'The location associated with this resource. Implicit FK to option_value row in booking_resource_location option_group.',
     `weight` int NOT NULL   ,
     `is_unlimited` tinyint NOT NULL  DEFAULT 0 ,
     `is_active` tinyint   DEFAULT 1 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_unlimited`(
        is_unlimited
  )
  ,     INDEX `index_is_active`(
        is_active
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )

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
     `unit_id` varchar(512)    COMMENT 'The unit associated with this config option. Implicit FK to option_value row in booking_size_unit option_group.',
     `weight` int unsigned NOT NULL   ,
     `is_active` tinyint   DEFAULT 1 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_active`(
        is_active
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )

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
     `booking_id` int unsigned NOT NULL   COMMENT 'FK to Booking ID',
     `resource_id` int unsigned NOT NULL   COMMENT 'FK to resource ID',
     `config_id` int unsigned    COMMENT 'FK to resource configuration option ID',
     `start` datetime NOT NULL   ,
     `end` datetime NOT NULL   ,
     `quantity` int NOT NULL   ,
     `note` text    ,
     `is_cancelled` tinyint   DEFAULT 0 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_cancelled`(
        is_cancelled
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )

,          CONSTRAINT FK_civicrm_booking_slot_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_slot_resource_id FOREIGN KEY (`resource_id`) REFERENCES `civicrm_booking_resource`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_slot_config_id FOREIGN KEY (`config_id`) REFERENCES `civicrm_booking_resource_config_option`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civicrm_booking_sub_slot
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_sub_slot` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  ,
     `slot_id` int unsigned    COMMENT 'FK to Slot ID',
     `resource_id` int unsigned    COMMENT 'FK to resource ID',
     `config_id` int unsigned    COMMENT 'FK to resource configuration option ID',
     `time_required` datetime NOT NULL   ,
     `quantity` int NOT NULL   ,
     `note` text    ,
     `is_cancelled` tinyint   DEFAULT 0 ,
     `is_deleted` tinyint   DEFAULT 0
,
    PRIMARY KEY ( `id` )

    ,     INDEX `index_is_cancelled`(
        is_cancelled
  )
  ,     INDEX `index_is_deleted`(
        is_deleted
  )

,          CONSTRAINT FK_civicrm_booking_sub_slot_slot_id FOREIGN KEY (`slot_id`) REFERENCES `civicrm_booking_slot`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_sub_slot_resource_id FOREIGN KEY (`resource_id`) REFERENCES `civicrm_booking_resource`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_booking_sub_slot_config_id FOREIGN KEY (`config_id`) REFERENCES `civicrm_booking_resource_config_option`(`id`) ON DELETE CASCADE
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


