<?php

/**
 * BookingResourceConfigOption.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_resource_config_option_create_spec(&$spec) {
  //$spec['magicword']['api.required'] = 1;
}

/**
 * BookingResourceConfigOption.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_resource_config_option_create($params) {
  return _civicrm_api3_basic_create('CRM_Booking_BAO_ResourceConfigOption', $params);

}

/**
 * BookingResourceConfigOptiont.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_resource_config_option_get($params) {
  return _civicrm_api3_basic_get('CRM_Booking_BAO_ResourceConfigOption', $params);
}




