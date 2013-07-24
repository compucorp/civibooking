DROP TABLE IF EXISTS `civicrm_booking_config`;
DROP TABLE IF EXISTS `civicrm_booking_resource_criteria`;
DROP TABLE IF EXISTS `civicrm_booking_slot`;
DROP TABLE IF EXISTS `civicrm_booking_resource_config`;
DROP TABLE IF EXISTS `civicrm_booking_resource`;
DROP TABLE IF EXISTS `civicrm_booking_cancellation`;
DROP TABLE IF EXISTS `civicrm_booking_payment`;
DROP TABLE IF EXISTS `civicrm_booking`;



-- /*******************************************************
-- *
-- *
-- * A civicrm booking config.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_config` (
     `id` int unsigned NOT NULL ,
     `financial_type_default` int unsigned,
     `day_start_at` time NOT NULL,
     `day_ends_at` time NOT NULL,
     `log_confirmation_email` tinyint DEFAULT 0,
     `selected_email_address` varchar(255),
     `cc_email_address` varchar(255),
     `bcc_email_address` varchar(255),
     `slot_avaliable_colour` varchar(10) NOT NULL ,
     `slot_unavaliable_colour` varchar(10) NOT NULL,
    PRIMARY KEY ( `id` )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking` (
     `id` int unsigned NOT NULL AUTO_INCREMENT ,
     `primary_contact_id` int unsigned NOT NULL ,
     `secondary_contact_id` int unsigned  ,
     `po_number` varchar(255) NOT NULL,
     `status_id` int unsigned NOT NULL,
     `title` varchar(50) NOT NULL,
     `description` varchar(255) ,
     `discount_amount` decimal(20,2)  ,
     `notes` text ,
     `participants_estimate` varchar(255) ,
     `participants_actual` varchar(255) ,
     `paymeny_status` int unsigned,
     `is_deleted` tinyint DEFAULT 0,
     `created_by` int unsigned ,
     `created_date` datetime ,
     `updated_by` int unsigned ,
     `updated_date` datetime  ,
    PRIMARY KEY ( `id` ),
     CONSTRAINT FK_civibooking_primary_contact_id FOREIGN KEY (`primary_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,
     CONSTRAINT FK_civibooking_secondary_contact_id FOREIGN KEY (`secondary_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;



-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking resource.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource` (
     `id` int unsigned NOT NULL AUTO_INCREMENT ,
     `label` varchar(255) NOT NULL ,
     `description` varchar(255) NOT NULL ,
     `weight` int unsigned NOT NULL ,
     `resource_type` int unsigned NOT NULL  COMMENT 'Type of resource, link to option group',
     `resource_location` int NOT NULL   COMMENT 'Location of resource, link to location',
     `is_unlimited` tinyint,
     `is_active` tinyint,
     `is_deleted` tinyint,
    PRIMARY KEY ( `id` )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;


-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking resource config.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource_config` (
     `id` int unsigned NOT NULL AUTO_INCREMENT,
     `resource_id` int unsigned NOT NULL ,
     `label` varchar(255) NOT NULL ,
     `value` varchar(255) ,
     `price` decimal(20,2) NOT NULL,
     `max_size` int unsigned NOT NULL ,
     `unit_id` int unsigned NOT NULL,
     `weight` int unsigned NOT NULL,
     `is_active` tinyint ,
    PRIMARY KEY ( `id` ),
     CONSTRAINT FK_civibooking_resource_config_resource_id FOREIGN KEY (`resource_id`) REFERENCES `civicrm_booking_resource`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking slot.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_slot` (
     `id` int unsigned NOT NULL AUTO_INCREMENT,
     `booking_id` int unsigned  NOT NULL   ,
     -- `resource_id` varchar(255) NOT NULL  ,
     `resource_config_id` int unsigned  NOT NULL  ,
     `start` datetime NOT NULL,
     `end` datetime NOT NULL,
     `price` decimal(20,2) NOT NULL,
     `notes` text ,
     `parent_id` int unsigned,
     `is_deleted` tinyint,
    PRIMARY KEY ( `id` ),
     CONSTRAINT FK_civibooking_slot_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE,
     CONSTRAINT FK_civibooking_slot_rcid_id FOREIGN KEY (`resource_config_id`) REFERENCES `civicrm_booking_resource_config`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;



-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking payment.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_payment` (
     `id` int unsigned NOT NULL AUTO_INCREMENT ,
     `booking_id` int unsigned  NOT NULL ,
     `contribution_id` int unsigned  NOT NULL,
    PRIMARY KEY ( `id` ),
     CONSTRAINT FK_civibooking_payment_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE,
     CONSTRAINT FK_civibooking_payment_contribution_id FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking cancellation.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_cancellation` (
     `id` int unsigned NOT NULL AUTO_INCREMENT ,
     `booking_id` int unsigned  NOT NULL ,
     `cancallation_date` datetime  NOT NULL,
     `additional_charges` decimal(20,2) NOT NULL,
     `comment` text ,
    PRIMARY KEY ( `id` ),
     CONSTRAINT FK_civibooking_cancellation_booking_id FOREIGN KEY (`booking_id`) REFERENCES `civicrm_booking`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * 
-- *
-- * A civicrm booking resource criteria.
-- *
-- *******************************************************/
CREATE TABLE `civicrm_booking_resource_criteria` (
     `id` int unsigned NOT NULL AUTO_INCREMENT ,
     `resource_id` int unsigned NOT NULL ,
     `criteria_id` INT unsigned NOT NULL   COMMENT 'Link to resource criteria option group',
     
    PRIMARY KEY ( `id` ),
     CONSTRAINT FK_civibooking_resource_criteria_booking_id FOREIGN KEY (`resource_id`) REFERENCES `civicrm_booking_resource`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

