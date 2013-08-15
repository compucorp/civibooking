<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * FIXME
 */
class CRM_Civibooking_BAO_BookingConfigTest extends CiviUnitTestCase {
  function setUp() {
    // If your test manipulates any SQL tables, then you should truncate
    // them to ensure a consisting starting point for all tests
    // $this->quickCleanup(array('example_table_name'));
    parent::setUp();
  }

  function tearDown() {
    parent::tearDown();
  }

  function testCreate(){
    $params = array("label" => "conference room1",
                    "description" => "description",
                    "weight" => 1,
                    "resource_type" => "tc",
                    "resource_location" => "location1",
                    "is_unlimited" => 1,
                    "is_active" => 1,
                    "is_deleted" => 0) ;
    $dao = CRM_Civibooking_BAO_Resource::create($params);
    $this->assertNotEmpty($dao->toArray());
  }
}
