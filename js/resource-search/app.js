CRM.App = new Backbone.Marionette.Application();

CRM.App.addRegions({
  mainRegion: "#search-layout",
});

CRM.App.on("initialize:after", function(){
  Backbone.history.start();

});

