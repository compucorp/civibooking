CRM.ResourceSearch.module('Collection', function(Collection, ResourceSearch, Backbone, Marionette, $, _){

  Collection.ResourceResultList = Backbone.Collection.extend({

    model: ResourceSearch.Model.ResourceResult,

    initialize: function(){
      var self = this;
      CRM.ResourceSearch.vent.on("search:query", function (searchQuery){
       self.search(searchQuery);
      });
  
    },
    
    search: function(searchQuery){
      var self = this;
      this.fetchResource(searchQuery, function (resources){
        self.reset(resources);
      });
      //this.previousSearch = searchQuery;
    },
     
    fetchResource: function(searchQuery, callback){
      var ajaxURL = CRM.url('civicrm/booking/resource/search');
      var self = this;
      $.ajax({
        url: ajaxURL,
        method: 'GET',
        //dataType: 'jsonp',
        data: searchQuery,
        success: function (response) {
          var searchResults = [];   
          _.each(JSON.parse(response), function(item){
            searchResults[searchResults.length] = new ResourceSearch.Model.ResourceTable({
              result: item.result
            });
          });
          callback(searchResults);
        }
      });
    }
  });
  
  Collection.ResourceTableList = Backbone.Collection.extend({
    model: ResourceSearch.Model.ResourceResult,  
  });
  
});
