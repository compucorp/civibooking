<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * FIXME
 */
class CRM_Civibooking_BAO_ResourceTest extends CiviUnitTestCase {
  function setUp() {
    // If your test manipulates any SQL tables, then you should truncate
    // them to ensure a consisting starting point for all tests
    $this->quickCleanup(array('civicrm_booking_resource'));
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

  function testSearch(){
    $testObjects = CRM_Core_DAO::createTestObject('CRM_Civibooking_DAO_Resource')->toArray();
    $params = array("resource_id" => $testObjects['id'],
                    "resource_type" => $testObjects['resource_type']);
    $resources = CRM_Civibooking_BAO_Resource::search($params);
    $this->assertNotNull($resources);
    $this->assertNotEmpty($resources);
    $this->assertEquals(1, sizeof($resources));
  }

}