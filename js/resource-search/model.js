CRM.ResourceSearch.module('Model', function(Model, ResourceSearch, Backbone, Marionette, $, _){
  
  Model.Resource = Backbone.Model.extend({
    
    defaults: {
      resources: null,
    },

    validate: function(attrs, options) {
      var errors = {}
      if( ! _.isEmpty(errors)){
        return errors;
      }
    }
  });

  Model.ResourceResult = Backbone.Model.extend({
    
    defaults: {
      id: null,
      description: null,
      is_unlimited: null,
      label: null,
      resource_location: null,
      resource_type: null,
      slots: null,
    },

    validate: function(attrs, options) {
      var errors = {}
      if( ! _.isEmpty(errors)){
        return errors;
      }
    }
  });
  


  
});
