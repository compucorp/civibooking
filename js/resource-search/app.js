CRM.ResourceSearch = new Backbone.Marionette.Application();

CRM.ResourceSearch.addRegions({
  searchForm: "#search-form",
  searchResult: "#search-result"
});

CRM.ResourceSearch.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});


