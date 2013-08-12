CRM.ResourceSearch.module('Model', function(Model, ResourceSearch, Backbone, Marionette, $, _){

  Model.Basket = Backbone.Model.extend({
    
    defaults: {
      id: null,
    },

    validate: function(attrs, options) {
      var errors = {}
      if( ! _.isEmpty(errors)){
        return errors;
      }
    }
  
  });
  
  Model.ResourceTable = Backbone.Model.extend({
    /*
    defaults: {
      result: null,
    },*/
  });

  Model.ResourceRow = Backbone.Model.extend({
    /*
    defaults: {
      id: null,
      description: null,
      is_unlimited: null,
      label: null,
      resource_location: null,
      resource_type: null,
      date: null,
      slots: null,
    },*/

  });
  


  
});
