<?php

/**
 * Slot.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_slot_create_spec(&$spec) {

}

/**
 * Slot.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_slot_create($params) {
  if(CRM_Utils_Array::value('id', $params)){
    if(!CRM_Booking_BAO_Slot::isValid($params)){
      return civicrm_api3_create_error('Unable to create slot. Please check the slot date time is availables.');
    }
  }
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}


/**
 * Slot.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_slot_get($params) {
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
function _civicrm_api3_slot_delete_spec(&$spec) {

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
function civicrm_api3_slot_delete($params) {
  if (CRM_Booking_BAO_Slot::del($params['id'])) {
    return civicrm_api3_create_success($params, $params, 'slot', 'delete');
  }
  else {
   return civicrm_api3_create_error('Could not delete slot.');
  }
}


/**
 * Slot.Validate API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_slot_validate($params) {
  $resources = $params['resources'];
  $isValid = TRUE;
  $errorResources = array();
  $slot = array();
  foreach ($resources as $key => $resource) {
    if(!CRM_Booking_BAO_Slot::isValid($resource)){
      $errorResources[] = $resource;
    }
    //$slot[] = $resource;
  }
  /*$num = sizeof($slot);
  for($i=0; $i<=$num; $i++){
    $currentSlot = $slot[$i];
    for($j=$i; $j<=$num; $j++){
      $testSlot = $slot[$j];

    }
  }*/
  if(!empty($errorResources)){
    $isValid = FALSE;
  }
  return civicrm_api3_create_success(array('is_valid' => $isValid, 'error_resources' => $errorResources), $params, 'slot', 'validate');
}


