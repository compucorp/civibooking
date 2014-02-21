<?php

class CRM_Booking_BAO_Resource extends CRM_Booking_DAO_Resource {

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
   * @return object CRM_Booking_BAO_Resource object
   * @access public
   * @static
   */
  static function create(&$params) {
    $resourceDAO = new CRM_Booking_DAO_Resource();
    $resourceDAO->copyValues($params);
    return $resourceDAO->save();
  }


    /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
     * @return object CRM_Booking_DAO_Resource object on success, null otherwise
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $resource = new CRM_Booking_DAO_Resource();
    $resource->copyValues($params);
    if ($resource->find(TRUE)) {
      CRM_Core_DAO::storeValues($resource, $defaults);
      return $resource;
    }
    return NULL;
  }


  /**
   * Function to delete Resource
   *
   * @param  int  $id     Id of the Resource to be deleted.
   *
   * @return boolean
   *
   * @access public
   * @static
   */
  static function del($id) {
    $resource = new CRM_Booking_DAO_Resource();
    $resource->id = $id;
    $resource->is_deleted = 1;
    return $resource->save();
  }


  static function getResourceTypeGroupId(){
    $result = civicrm_api('OptionGroup', 'get',array('version' => 3, 'name' => 'booking_resource_type'));
    $typeGroupId = $result['id'];
    return $typeGroupId;
  }


  static function getResourcesByType($type, $includeLimited = false) {
    $typeGroupId = self::getResourceTypeGroupId();
    $params = array(1 => array( $type, 'String'));

    // Build query of resources that can be booked.
    // Only return resources that are enabled (is_active = 1)  that are not deleted (is_deleted <> 1)
    $query = "
    SELECT civicrm_booking_resource.id,
           civicrm_booking_resource.set_id,
           civicrm_booking_resource.label,
           civicrm_booking_resource.description,
           civicrm_booking_resource.weight,
           civicrm_booking_resource.type_id,
           civicrm_booking_resource.location_id,
           civicrm_booking_resource.is_unlimited
     FROM  civicrm_booking_resource
     WHERE civicrm_booking_resource.type_id = %1
     AND civicrm_booking_resource.is_active = 1
     AND civicrm_booking_resource.is_deleted <> 1";

    $resources = array();
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
       $resources[$dao->id] = array(
        'id' => $dao->id,
        'set_id' => $dao->set_id,
        'label' => $dao->label,
        'description' => $dao->description,
        'weight' => $dao->weight,
        'type_id' => $dao->type_id,
        'location_id' => $dao->location_id,
        'is_unlimited' => $dao->is_unlimited,
      );
    }
    return $resources;
  }




  static function getResourceTypes($includeLimited = false){

    $typeGroupId = self::getResourceTypeGroupId();
    if($typeGroupId){

      $whereClause = " WHERE 1";
      if (!$includeLimited) {
          $whereClause .= " AND civicrm_booking_resource.is_unlimited = 0";
      }

      $query = "
          SELECT civicrm_option_value.id,
                 civicrm_option_value.label,
                 civicrm_option_value.value,
                 civicrm_option_value.option_group_id
          FROM civicrm_option_value
          INNER JOIN civicrm_booking_resource ON civicrm_option_value.option_group_id = $typeGroupId
          AND civicrm_booking_resource.type_id = civicrm_option_value.value
          AND civicrm_option_value.is_active = 1";

       $query .= "$whereClause";


      $resourceTypes = array();
      $dao = CRM_Core_DAO::executeQuery($query);
      while ($dao->fetch()) {
         $resourceTypes[$dao->value] = array(
          'id' => $dao->value,
          'label' => $dao->label,
          'value' => $dao->value,
          'option_group_id' => $dao->option_group_id
        );
      }

      return $resourceTypes;
    }else{
      CRM_Core_Error::fatal('Civibooking resource type option group appears to be missing.');
    }

  }


  /**
   * update the is_active flag in the db
   *
   * @param int      $id        id of the database record
   * @param boolean  $is_active value we want to set the is_active field
   *
   * @return Object             DAO object on sucess, null otherwise
   * @static
   */
  static function setIsActive($id, $is_active) {
    return CRM_Core_DAO::setFieldValue('CRM_Booking_DAO_Resource', $id, 'is_active', $is_active);
  }


  /**
   * Sets the Resource's is_deleted flag in the database
   *
  public function delete() {
    $this->is_deleted = 1;
    $this->save();
  }*/


}
