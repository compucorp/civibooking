INSERT INTO `civicrm_booking_resource_config_set` (`id`, `title`, `weight`, `is_enabled`, `is_deleted`) VALUES
(1, 'Training room config set', 1, 1, 0),
(2, 'Meeting room config set', 2, 1, 0),
(3, 'Projector config set', 3, 1, 0),
(4, 'Laptop config set', 4, 1, 0),
(5, 'Tea/coffee configuration', 5, 1, 0);

INSERT INTO `civicrm_booking_resource_config_option` (`id`, `set_id`, `label`, `price`, `max_size`, `unit_id`, `weight`, `is_enabled`) VALUES
(1, 1, 'Boardroom', 200.00, '20', 'people', 1, 1),
(2, 1, 'Open', 200.00, '20', 'people', 2, 1),
(3, 2, 'Open space', 300.00, '10', 'table', 3, 1),
(4, 2, 'Cafe style', 500.00, '10', 'table', 4, 1),
(5, 3, 'SVGA', 50.00, '0', 'piece', 5, 1),
(6, 3, 'WUXGA', 90.00, '0', 'piece', 6, 1),
(7, 4, 'Macbook Air', 40.00, '0', 'laptop', 7, 1),
(8, 4, 'Macbook Pro', 60.00, '0', 'laptop', 8, 1),
(9, 5, 'Starbucks', 5.00, '0', 'head', 9, 1),
(10, 5, 'Costa', 4.00, '0', 'head', 10, 1);


INSERT INTO `civicrm_booking_resource` ( `set_id`, `label`, `description`, `weight`, `resource_type`, `resource_location`, `is_unlimited`, `is_enabled`, `is_deleted`) VALUES
(1, 'Training room 1', NULL, 1, 'room', '1', 0, 1, 0),
(2, 'Meeting room 1', NULL, 2, 'room', '1', 0, 1, 0),
(2, 'Meeting room 2', NULL, 3, 'room', '1', 0, 1, 0),
(2, 'Meeting room 3', NULL, 4, 'room', '1', 0, 1, 0),
(3, 'Projector 1', NULL, 5, 'projector', '1', 0, 1, 0),
(3, 'Projector 2', NULL, 6, 'projector', '1', 0, 1, 0),
(4, 'Laptop 1', NULL, 7, 'laptop', '1', 1, 1, 0),
(4, 'Laptop 2', NULL, 8, 'laptop', '1', 1, 1, 0),
(5, 'Tea/coffee set 1', NULL, 9, 'tea_coffee', '1', 1, 1, 0),
(5, 'Tea/coffee set 2', NULL, 10, 'tea_coffee', '1', 1, 1, 0),
(5, 'Tea/coffee set 3', NULL, 11, 'tea_coffee', '1', 1, 1, 0),
(1, 'Training room 3', NULL, 12, 'room', '1', 0, 1, 0);
