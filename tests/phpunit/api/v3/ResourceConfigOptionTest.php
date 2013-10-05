<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * FIXME
 */
class api_v3_ResourceConfigOptionTest extends CiviUnitTestCase {

  protected $entity = 'ResourceConfigOption';
  //protected $params;

  function setUp() {
    // If your test manipulates any SQL tables, then you should truncate
    // them to ensure a consisting starting point for all tests
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
    $testObject = CRM_Core_DAO::createTestObject('CRM_Booking_DAO_ResourceConfigOption')->toArray();
    $result = $this->callAPIAndDocument('ResourceConfigOption', 'create', $testObject, __FUNCTION__, __FILE__);
    $this->assertEquals(1, $result['count']);
    $this->assertEquals($testObject['id'], $result['id']);

  }



  public function testGet(){
    $testObject = CRM_Core_DAO::createTestObject('CRM_Booking_DAO_ResourceConfigOption')->toArray();
    $configOption = $this->callAPISuccess('ResourceConfigOption', 'Create', $testObject);
    $result = $this->callAPISuccess('ResourceConfigOption', 'Get', array(
      'id' => $configOption['id'],
      'sequential' => 1,
      )
    );
    $this->assertEquals(1, $result['count']);
    $this->assertEquals($testObject['id'], $result['id']);
  }

}
