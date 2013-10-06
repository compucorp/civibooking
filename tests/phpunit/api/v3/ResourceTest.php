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
    $this->quickCleanup(array('civicrm_booking_resource_config_option'));
    $this->quickCleanup(array('civicrm_booking_resource_config_set'));

    parent::setUp();
  }

  function tearDown() {
    parent::tearDown();
  }

  public function testCreate() {
    // create an example resource
    $resource = CRM_Core_DAO::createTestObject('CRM_Booking_DAO_Resource')->toArray();
    $result = $this->callAPIAndDocument('Resource', 'create', $resource, __FUNCTION__, __FILE__);
    $this->assertEquals(1, $result['count']);
    $this->assertGreaterThan(0, $result['id']);

  }

  public function testGetChain(){
    $set = $this->callAPISuccess('ResourceConfigSet', 'Create', array(
      "title" =>  "title_1",
      "weight"=> 1,
      "is_enabled"=> 1,
      "is_deleted"=> 0,)
    );

    $this->callAPISuccess('ResourceConfigOption', 'Create', array(
      "set_id" => $set['id'],
      "label"=>"label_3",
      "price"=> 20.0,
      "max_size"=> "max_size_3",
      "unit_id"=> "unit_id_3",
      "weight"=> 3,
      "is_enabled"=> 1)
    );

    $this->callAPISuccess('ResourceConfigOption', 'Create', array(
      "set_id" => $set['id'],
      "label"=>"label_4",
      "price"=> 30.0,
      "max_size"=> "max_size_4",
      "unit_id"=> "unit_id_4",
      "weight"=> 3,
      "is_enabled"=> 1)
    );

    $testObject = CRM_Core_DAO::createTestObject('CRM_Booking_DAO_Resource')->toArray();
    $params = array(
      'set_id' => $set['id'],
      'label' => $testObject['label'],
      'description' => $testObject['description'],
      'weight' => $testObject['weight'],
      'resource_type' => $testObject['resource_type'],
      'resource_location' => $testObject['resource_location'],
      'is_unlimited' => 0,
      'is_enabled' => 1,
      'is_deleted' => 0
    );


    $resource =  $this->callAPISuccess('Resource', 'Create', $params);
    $result = $this->callAPISuccess('Resource', 'Get', array(
      'id' => $resource['id'],
      'sequential' => 1,
      'api.resource_config_set.get' => array(
        'id' => '$value.set_id',
        'api.resource_config_option.get' => array(
          'set_id' => '$value.id',
        ),
      )
    ));

    $this->assertEquals(1, $result['count']);
    $this->assertEquals(1, $result['values'][0]['api.resource_config_set.get']['count']);
    $this->assertEquals(2, $result['values'][0]['api.resource_config_set.get']['values'][0]['api.resource_config_option.get']['count']);

  }
}
