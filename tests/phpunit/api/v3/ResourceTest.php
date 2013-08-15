<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * FIXME
 */
class api_v3_ResourceTest extends CiviUnitTestCase {
  function setUp() {
    // If your test manipulates any SQL tables, then you should truncate
    // them to ensure a consisting starting point for all tests
    $this->quickCleanup(array('civicrm_booking_resource'));
    parent::setUp();
  }

  function tearDown() {
    parent::tearDown();
  }

  public function testCreateUpdate() {
    // create an example resource
    $resource = CRM_Core_DAO::createTestObject('CRM_Civibooking_DAO_Resource')->toArray();


    var_dump($resource);

    $result = $this->callAPISuccess('BookingResource', 'create', array(
      'id' => $resource['id'],
      'description' => 'new description',
      'resource_type' => '',
    ));

    var_dump($result);

    /*
    $this->assertTrue(is_numeric($role['id']));
    $this->assertTrue(is_numeric($role['job_id']));
    $this->assertNotEmpty($role['title']);
    $this->assertNotEmpty($role['location']);

    // update the role
    $result = $this->callAPISuccess('HRJobRole', 'create', array(
      'id' => $role['id'],
      'description' => 'new description',
      'location' => '',
    ));

    // check return format
    $this->assertEquals(1, $result['count']);
    foreach ($result['values'] as $roleResult) {
      $this->assertEquals('new description', $roleResult['description']);
      $this->assertEquals('', $roleResult['location']); // BUG: $roleResult['location'] === 'null'
    }*/
  }
}
