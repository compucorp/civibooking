<?php

/**
 * AdhocCharges.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_adhoc_charges_create_spec(&$spec) {
  $spec['booking_id']['api.required'] = 1;
  $spec['item_id']['api.required'] = 1;
  $spec['quantity']['api.required'] = 1;
}

/**
 * AdhocCharges.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_adhoc_charges_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}


/**
 * AdhocCharges.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_adhoc_charges_get($params) {
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
function _civicrm_api3_adhoc_charges_delete_spec(&$spec) {

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
function civicrm_api3_adhoc_charges_delete($params) {
  if (CRM_Booking_BAO_AdhocCharges::del($params['id'])) {
    return civicrm_api3_create_success($params, $params, 'AdhocCharges', 'delete');
  }
  else {
   return civicrm_api3_create_error('Could not delete adhoc charges.');
  }
}









