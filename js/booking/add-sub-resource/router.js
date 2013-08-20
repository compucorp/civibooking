CRM.BookingApp.module('AddSubResource', function(AddSubResource, BookingApp, Backbone, Marionette, $, _){

  AddSubResource.Router = Marionette.AppRouter.extend({
    appRoutes: {
      "": "default",
    }
  });

  /*
  CRM.BookingApp.vent.on("search:query", function(queryString){
    Backbone.history.navigate("search?" + queryString,  {trigger: true});
  });
  */

  var controller = {
    default: function(){
      console.log('default');

    }
  };

  CRM.BookingApp.addInitializer(function(){
    new CRM.BookingApp.AddSubResource.Router({
      controller: controller
    });
    //Initializer layout
    var view = new AddSubResource.ResourceTableView();
    console.log(view);
    CRM.BookingApp.main.show(view);
  });
});
