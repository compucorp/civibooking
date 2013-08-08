CRM.ResourceSearch.module('AppRouting', function(AppRouting, ResourceSearch, Backbone, Marionette, $, _){
  
  ResourceSearch.Router = Marionette.AppRouter.extend({
    appRoutes: {
      "search?:queryString" : "search"
    }
  });

  ResourceSearch.vent.on("search:query", function(queryString){
    Backbone.history.navigate("search?" + queryString,  {trigger: true});
  });

  var controller = {
    search: function(queryString){
      var resourceCollection = new CRM.ResourceSearch.Collection.ResourceResultList(); 
      var resourceCollectionView = new CRM.ResourceSearch.View.ResourceCollection({ collection: resourceCollection});
      var searchForm = new CRM.ResourceSearch.View.SearchForm();
      CRM.ResourceSearch.searchForm.attachView(searchForm); //attach the existing view
      CRM.ResourceSearch.searchResult.show(resourceCollectionView);
      ResourceSearch.vent.trigger("search:query", queryString);
    }
  };

  ResourceSearch.addInitializer(function(){
    new CRM.ResourceSearch.Router({
      controller: controller
    });
    //Initializer layout  
    CRM.ResourceSearch.searchForm.show(new CRM.ResourceSearch.View.SearchForm());

  });
});