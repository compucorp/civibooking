<?php

class CRM_Civibooking_BAO_Resource extends CRM_Civibooking_DAO_Resource {

  //TODO:: Implement retrive function
  static function retrive($params) {
  	$result = NULL;
  	return $result;
  }

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
    $resourceDAO = new CRM_Civibooking_DAO_Resource();
    $resourceDAO->copyValues($params);
    return $resourceDAO->save();
  }

  
  static function search($params){
    $whereClause = 'WHERE 1';
    if (is_array($params) && !empty($params)) {
    	if(isset($params['resource_id'])){
    		$whereClause .= " AND civicrm_booking_resource.id = " . CRM_Utils_Type::escape($params['resource_id'], 'Integer');
    	}
    	if(isset($params['resource_type'])){
    		$whereClause .= " AND civicrm_booking_resource.resource_type = '" . CRM_Utils_Type::escape($params['resource_type'], 'String') . "'";
    	}

    }

    //FIXME:: Get group Id from database
    $typeGroupId = 97;
    $locationGroupId = 98;

    $query = "
    SELECT civicrm_booking_resource.id,
    			 civicrm_booking_resource.label,
    			 civicrm_booking_resource.description,
    			 civicrm_booking_resource.weight,
    			 civicrm_option_resource_type.value as resource_type,
    			 civicrm_booking_resource.resource_location as resource_location,
    			 civicrm_booking_resource.is_unlimited
     FROM  civicrm_booking_resource 
		 LEFT JOIN  civicrm_option_value as civicrm_option_resource_type ON civicrm_option_resource_type.option_group_id = $typeGroupId
		 																																 AND civicrm_option_resource_type.value = civicrm_booking_resource.resource_type ";
		 //LEFT JOIN  civicrm_option_value as civicrm_option_resource_location ON civicrm_option_resource_location.option_group_id = $locationGroupId 
		 //																																 AND civicrm_option_resource_location.value = civicrm_booking_resource.resource_location

     $query .= "$whereClause";


    $resources = array();
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
    	 $resources[$dao->id] = array(
        'id' => $dao->id,
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
}
