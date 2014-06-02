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

  /**
   * Return an array of all resources of a given resource type
   *
   * @param $type
   * @param bool $includeLimited
   * @return array
   */
  static function getResourcesByType($type, $includeLimited = false) {
    $typeGroupId = self::getResourceTypeGroupId();
    $params = array(1 => array( $type, 'String'));

    // Build query of resources that can be booked.
    // Only return resources that are enabled (is_active = 1)  that are not deleted (is_deleted <> 1)
    $query = "
    SELECT r.id,
           r.set_id,
           r.label,
           r.description,
           r.weight,
           r.type_id,
           r.location_id,
           r.is_unlimited
     FROM  civicrm_booking_resource r
     WHERE r.type_id = %1
     AND r.is_active = 1
     AND r.is_deleted <> 1
     ORDER BY r.weight";

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

    /**
     * Return an array of all resource types
     *
     * @param bool $includeLimited
     * @return array
     * @throws Exception
     */
    static function getResourceTypes($includeLimited = false){

    $typeGroupId = self::getResourceTypeGroupId();
    if($typeGroupId){

      $whereClause = " WHERE 1";
      if (!$includeLimited) {
          $whereClause .= " AND r.is_unlimited = 0";
      }

      $query = "
          SELECT v.id,
                 v.label,
                 v.value,
                 v.option_group_id,
                 r.weight
          FROM civicrm_option_value v
          INNER JOIN civicrm_booking_resource r
          ON v.option_group_id = $typeGroupId
          AND r.type_id = v.value
          AND v.is_active = 1";

      $query .= "$whereClause";
      $query .= " ORDER BY r.weight";

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