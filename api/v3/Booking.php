<?php

/**
 * Booking.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_booking_create_spec(&$spec) {
  $spec['primary_contact_id']['api.required'] = 1;
  $spec['title']['api.required'] = 1;
  $spec['status_id']['api.required'] = 1;
  //$spec['created_by']['api.required'] = 1;
  //$spec['created_date']['api.required'] = 1;

}

/**
 * Booking.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_booking_create($params) {
  $bookingBAO = CRM_Booking_BAO_Booking::create($params);
   _civicrm_api3_object_to_array($bookingBAO, $bookingArray[$bookingBAO->id]);
  return civicrm_api3_create_success($bookingArray, $params, 'Booking', 'create');
}


/**
 * Booking.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_booking_get($params) {
  if(empty($params['options']['limit'])){
    $params['options']['limit'] = 0;
  }
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}



/**
 * Booking.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_Booking_delete($params) {
  if (CRM_Booking_BAO_Booking::del($params['id'])) {
    return civicrm_api3_create_success($params, $params, 'booking', 'delete');
  }
  else {
   return civicrm_api3_create_error('Could not delete booking.');
  }
}






