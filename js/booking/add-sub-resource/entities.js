
CRM.BookingApp.module('Entities', function(Entities, BookingApp, Backbone, Marionette, $, _){

  Entities.SubResource = Backbone.Model.extend({
    defaults: {
      sub_resources: {},
      resources: {},
      sub_total: 0,
      adhoc_charges: {total:0},
      discount_amount:0,
      total_price:0

    },
  });

  Entities.AddSubResource = Backbone.Model.extend({
    defaults: {
      parent_ref_id: null,
      ref_id: null,
      resource: {id : null, label :null},
      configuration: {id : null, label :null, price :0},
      quantity: 0,
      time_required: null,
      note: null,
      price_estimate: 0,
    },
  });

  Entities.AdhocCharges = Backbone.Model.extend({
    defaults: {
      items: {},
      note: null,
      total: 0,
    },
  });

});
