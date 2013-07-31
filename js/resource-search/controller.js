CRM.App.module('Controller', function(Controller, App, Backbone, Marionette, $, _){
  
  App.Router = Marionette.AppRouter.extend({
    appRoutes: {
      "" : "start"
    }
    
  });

  var controller = {
    start: function() {
      console.log('starting layout');
      var layout = new App.Layout();
      App.mainRegion.show(layout);
      layout.searchForm.show(new App.SearchForm.View());
    },
    
  };

  App.addInitializer(function(){
    new App.Router({
      controller: controller
    });
  });
});