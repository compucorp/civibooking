CRM.BookingApp.module('Entities', function(Entities, BookingApp, Backbone, Marionette, $, _){

  Entities.SubResource = Backbone.Model.extend({
    defaults: {
      sub_resources: {},
    },
  });

  Entities.AddSubResource = Backbone.Model.extend({
    defaults: {
      'parent_ref_id': null,
      'ref_id': null,
      'resource': {id : null, label :null},
      'configuration': {id : null, label :null, price :0},
      'quantity': 0,
      'time_reuired': null,
      'note': null,
      'price_estimate': 0,
    },
  });

});
