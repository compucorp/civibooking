<?php

/**
 * API Wrapper that preprocesses and post processes API Calls.
 */
class CRM_Booking_APIWrapper implements API_Wrapper {
  /**
   * Alter the parameters of the api request.
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    $this->fixParametersArray($apiRequest['params']);
    return $apiRequest;
  }

  /**
   * alter the result before returning it to the caller.
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

  /**
   * Fixes parameters array so that if chained API calls are made, any chained
   * fields with '$value.' are moved to the end of the array.
   *
   * @param array $params
   */
  private function fixParametersArray(&$params) {
    $chainedValues = array();

    foreach ($params as $parameter => &$value) {
      if (stripos($parameter, 'api.') === 0 && is_array($value)) {
        $this->fixParametersArray($value);
      }
      elseif (stripos($value, '$value.') === 0) {
        unset($params[$parameter]);
        $chainedValues[$parameter] = $value;
      }
    }

    $params = array_merge($params, $chainedValues);
  }

}