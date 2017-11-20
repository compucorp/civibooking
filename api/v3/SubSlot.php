<?php

/**
 * SubSlot.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_sub_slot_create_spec(&$spec) {

}

/**
 * SubSlot.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sub_slot_create($params) {
  //TODO:: Validate slot if it can be created
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);

}


/**
 * SubSlot.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sub_slot_get($params) {
  if(empty($params['options']['limit'])){
    $params['options']['limit'] = 0;
  }
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}



/**
 * Slot.Delete API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_sub_slot_delete_spec(&$spec) {

}


/**
 * Slot.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sub_slot_delete($params) {
  if (CRM_Booking_BAO_SubSlot::del($params['id'])) {
    return civicrm_api3_create_success($params, $params, 'SubSlot', 'delete');
  }
  else {
   return civicrm_api3_create_error('Could not delete SubSlot.');
  }
}








