<?php

class CRM_Booking_BAO_Resource extends CRM_Booking_DAO_Resource {

  static function getResourceTypeGroupId(){
    $result = civicrm_api('OptionGroup', 'get',array('version' => 3, 'name' => 'booking_resource_type'));
    $typeGroupId = $result['id'];
    return $typeGroupId;
  }

  static function getResourcesByType($type) {

    $typeGroupId = self::getResourceTypeGroupId();
    $params = array(1 => array( $type, 'String'));
    $query = "
    SELECT civicrm_booking_resource.id,
           civicrm_booking_resource.set_id,
           civicrm_booking_resource.label,
           civicrm_booking_resource.description,
           civicrm_booking_resource.weight,
           civicrm_booking_resource.resource_type,
           civicrm_booking_resource.resource_location,
           civicrm_booking_resource.is_unlimited
     FROM  civicrm_booking_resource
     WHERE civicrm_booking_resource.resource_type = %1";

    $resources = array();
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
       $resources[$dao->id] = array(
        'id' => $dao->id,
        'set_id' => $dao->set_id,
        'label' => $dao->label,
        'description' => $dao->description,
        'weight' => $dao->weight,
        'resource_type' => $dao->resource_type,
        'resource_location' => $dao->resource_location,
        'is_unlimited' => $dao->is_unlimited,
      );

    }
    return $resources;
  }




  static function getResourceTypes($includeLimited = true){

    $typeGroupId = self::getResourceTypeGroupId();
    if($typeGroupId){

      $whereClause = " WHERE 1";
      if (!$includeLimited) {
          $whereClause .= " AND civicrm_booking_resource.is_unlimited = 0";
      }

      $query = "
          SELECT civicrm_option_value.label, civicrm_option_value.value, civicrm_option_value.option_group_id
          FROM civicrm_option_value
          INNER JOIN civicrm_booking_resource ON civicrm_option_value.option_group_id = $typeGroupId
          AND civicrm_booking_resource.type_id = civicrm_option_value.id";

       $query .= "$whereClause";

      $resourceTypes = array();
      $dao = CRM_Core_DAO::executeQuery($query);
      while ($dao->fetch()) {
         $resourceTypes[$dao->value] = array(
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
   * Unsets the Resource's is_active flag in the database
   */
  public function disable() {
    $this->is_enabled = 0;
    $this->save();
  }

    /**
   * Sets the Resource's is_active flag in the database
   */
  public function enable() {
    $this->is_enabled = 1;
    $this->save();
  }

  /**
   * Sets the Resource's is_deleted flag in the database
   *
  public function delete() {
    $this->is_deleted = 1;
    $this->save();
  }*/


}
