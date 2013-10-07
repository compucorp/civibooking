<?php

/**
 * BookingPayment.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_booking_payment_get($params) {
  return _civicrm_api3_basic_get('CRM_Booking_BAO_Payment', $params);
}




