<?php

class CRM_Civibooking_BAO_ResourceConfigOption extends CRM_Civibooking_DAO_ResourceConfigOption {


    /**
   * takes an associative array and creates a resource object
   *
   * the function extract all the params it needs to initialize the create a
   * resource object. the params array could contain additional unused name/value
   * pairs
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   * @param array $ids    the array that holds all the db ids
   *
   * @return object CRM_Civibooking_BAO_Resource object
   * @access public
   * @static
   */
  static function create(&$params) {
    $resourceDAO = new CRM_Civibooking_DAO_ResourceConfigOption();
    $resourceDAO->copyValues($params);
    return $resourceDAO->save();
  }


}
