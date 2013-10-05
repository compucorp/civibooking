<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * FIXME
 */
class api_v3_ResourceConfigSetTest extends CiviUnitTestCase {

  protected $entity = 'ResourceConfigSet';
  //protected $params;

  function setUp() {
    // If your test manipulates any SQL tables, then you should truncate
    // them to ensure a consisting starting point for all tests
    $this->quickCleanup(array('civicrm_booking_resource_config_set'));
    $this->quickCleanup(array('civicrm_booking_resource_config_option'));
   // $this->params = $resultSet;
    parent::setUp();
  }


  function tearDown() {
    parent::tearDown();
  }

   /**
   * test create function
   */
  public function testCreate() {
    $params = array("title" =>  "title_1",
                    "weight"=> 1,
                    "is_enabled"=> 1,
                    "is_deleted"=> 0
                    );
    $result = $this->callAPIAndDocument('ResourceConfigSet', 'create', $params, __FUNCTION__, __FILE__);
    $this->assertEquals(1, $result['count']);
    $this->assertGreaterThan(0, $result['id']);

  }



  public function testGet(){
    $testObject = CRM_Core_DAO::createTestObject('CRM_Booking_DAO_ResourceConfigSet')->toArray();
    $configSet = $this->callAPISuccess('ResourceConfigSet', 'Create', $testObject);
    $result = $this->callAPISuccess('ResourceConfigSet', 'Get', array(
      'id' => $configSet['id'],
      'sequential' => 1,
      )
    );
    $this->assertEquals(1, $result['count']);
    $this->assertEquals($testObject['id'], $result['id']);
  }

  public function testGetChain(){
    $configSet = $this->callAPISuccess('ResourceConfigSet', 'Create', array(
                    "title" =>  "title_1",
                    "weight"=> 1,
                    "is_enabled"=> 1,
                    "is_deleted"=> 0,));

    $this->assertNotNull($configSet['id']);
    $this->assertTrue(is_numeric($configSet['id']));

    $this->callAPISuccess('ResourceConfigOption', 'Create', array(
                          "set_id" => $configSet['id'],
                          "label"=>"label_3",
                          "price"=> 20.0,
                          "max_size"=> "max_size_3",
                          "unit_id"=> "unit_id_3",
                          "weight"=> 3,
                          "is_enabled"=> 1));

    $this->callAPISuccess('ResourceConfigOption', 'Create', array(
                          "set_id" => $configSet['id'],
                          "label"=>"label_4",
                          "price"=> 30.0,
                          "max_size"=> "max_size_4",
                          "unit_id"=> "unit_id_4",
                          "weight"=> 3,
                          "is_enabled"=> 1));

    $result = $this->callAPISuccess('ResourceConfigSet', 'Get', array(
      'id' => $configSet['id'],
      'sequential' => 1,
      'api.resource_config_option.get' => array(
          'set_id' => $configSet['id'],
        ),
      )
    );

    $this->assertEquals(1, $result['count']);
    $this->assertEquals(2, $result['values'][0]['api.resource_config_option.get']['count']);
  }
}
