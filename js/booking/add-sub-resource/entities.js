var Entities = {
  SubResource: Backbone.Model.extend({
    defaults: {
      sub_resources: {},
      resources: {},
      sub_total: 0,
      adhoc_charges: {total:0},
      discount_amount:0,
      total_price:0

    },
  }),
  AddSubResource: Backbone.Model.extend({
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
  }),
  AdhocCharges: Backbone.Model.extend({
    defaults: {
      items: {},
      note: null,
      total: 0,
    },
  }),
};
