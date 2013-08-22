CRM.BookingApp.module('Entities', function(Entities, BookingApp, Backbone, Marionette, $, _){

  Entities.SubResourceModel = Backbone.Model.extend({
    defaults: {
      'resource': null,
      'configuration': null,
      'quantity': 0,
      'time_reuired': null,
      'note': null,
      'price_estimate': 0,
    },
  });

});
