<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CRM_Booking_HookTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  public function setUpHeadless() {

    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testMergeResourceOwner() {
    $mainOwner = civicrm_api3('Contact', 'create', array(
      'contact_type' => 'organization',
      'organization_name' => 'Main Owner',
    ));

    $otherOwner = civicrm_api3('Contact', 'create', array(
      'contact_type' => 'organization',
      'organization_name' => 'Other Owner',
    ));

    $resourceConfigSet =  civicrm_api3('ResourceConfigSet', 'create', array(
      'sequential' => 1,
      'title' => "test resource config set",
      'weight' => 1,
    ));

    civicrm_api3('ResourceConfigOption', 'create', array(
      'sequential' => 1,
      'owner_id' => $otherOwner['id'],
      'set_id' => $resourceConfigSet['id'],
      'label' => "Test",
      'price' => "5.00",
      'weight' => 1,
    ));

    $merger = new CRM_Dedupe_Merger();

    $pairs = array(array('dstID' => $mainOwner['id'], 'srcID' => $otherOwner['id']));

    $merger::merge($pairs);

    // Check that ownership of the resource config option has move to the main owner.
    $this->assertEquals(1, civicrm_api3('ResourceConfigOption', 'getcount', array(
      'owner_id' => $mainOwner['id'],
    )));
  }
}
