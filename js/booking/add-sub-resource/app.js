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
var Views = {
  /**
   * A form that use in a Modal that required the validate in the form
   *
   */
  BookingProcessModal: Marionette.View.extend({
    onRender: function() {
      var rules = this.createValidationRules();
      this.$('form').validate(rules);
    },
    /**
     *
     * @return {*} jQuery.validate rules
     */
    createValidationRules: function() {
      var rules = _.extend({}, CRM.validate.params);
      rules.rules || (rules.rules = {});
      this.triggerMethod("validateRules:create", this, rules);
      return rules;
    },
    onRenderError: function(errors){
      var view = this;
      _.each(errors, function(error) {
        console.log($(error).attr('for'));
         view.$('[name=' + $(error).attr('for') + ']').crmError($(error).text());
      });
    },
  }),
};
// see http://lostechies.com/derickbailey/2012/04/17/managing-a-modal-dialog-with-backbone-and-marionette/
var ModalRegion = Marionette.Region.extend({
  el: "#crm-booking-dialog",

  constructor: function(){
    Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
    this.on("show", this.showModal, this);
    this.on("close", this.hideModal, this);

  },

  showModal: function(view){
    //use CiviCRM diaglog
    cj('#crm-booking-dialog').dialog({
      modal: true,
      title: view.title,
      minWidth: '700',
      close: function() {
        cj( this ).dialog( "close" );
      }
    });
  },

  hideModal: function(){
    cj('#crm-booking-dialog').dialog().dialog("close");
  }
});
//Resource table view
var ResourceTableView = Marionette.View.extend({
  template: CRM._.template(CRM.$('#resource-table-template').html()),

  initialize: function(){
    if (CRM.$.trim(CRM.$("#sub_resources").val())) {
      this.model.attributes = JSON.parse(CRM.$.trim($("#sub_resources").val()));
    }
    this.model.attributes.total_price = CRM.$("#total_price").val();
    this.model.attributes.sub_total = CRM.$("#sub_total").val();
    //this.model.attributes.adhoc_charges = $("#adhoc_charge").val();
    this.model.attributes.discount_amount = CRM.$("#discount_amount").val();
  },

  onRender: function(){
    var subtotal = 0;
    var self = this;
    //init the current price for each resource
    this.$el.find("span[id^='resource-price-']").each(function(){
      var el = CRM.$(this);
      self.model.attributes.resources[el.data('ref')] = el.text();
    });
    var items = [];
    var template = CRM._.template(CRM.$('#sub-resource-row-template').html());
    CRM._.each(this.model.get('sub_resources'), function (item, key){
      self.$el.find("#crm-booking-sub-resource-table-" + item.parent_ref_id).append(template(item));
      priceCache[item.ref_id] = item.price_estimate;
      items.push(item);
    });
    this.$el.find("span[id^='resource-total-price-']").each(function(){
      var el = CRM.$(this);///////////////////////////
      var resourceTotalPrice = parseFloat(el.data('price'));
      _.find(items, function (item) {
        if(parseInt(item.parent_ref_id) === parseInt(el.data('ref'))){
            resourceTotalPrice += parseFloat(item.price_estimate);
        }
      });
      if(resourceTotalPrice != null){
        subtotal += resourceTotalPrice;
        el.text(resourceTotalPrice.toFixed(2));
        resourceTotal[el.data('ref')] = resourceTotalPrice.toFixed(2);
        self.$el.find('#crm-booking-sub-resource-row-' + el.data('ref')).show();
      }
    });
    this.model.attributes.sub_total = subtotal;
    this.model.attributes.total_price = (subtotal 
        + parseFloat(this.model.get("adhoc_charges").total)) 
        - parseFloat(this.model.get("discount_amount"));
    this.model.attributes.discount_amount = this.model.get("discount_amount");

    unlimitedTimeConfig = timeConfig;

    var subTotalText = this.model.get('sub_total');
    var adhocText = this.model.get('adhoc_charges').total;
    var discountText = this.model.get('discount_amount');
    var totalText = this.model.get('total_price');
    this.$el.find("#sub-total-summary").text(subTotalText.toFixed(2));
    this.$el.find("#ad-hoc-charge-summary").text(adhocText);
    try{
      this.$el.find("#ad-hoc-charge-summary").text(adhocText,toFixed(2));
    }catch(err){}
    this.$el.find("#discount_amount_dummy").val(discountText);
    this.$el.find("#total-price-summary").text(totalText.toFixed(2));
  },
  events: {
    'click .add-sub-resource': 'addSubResource',
    'click .edit-sub-resource': 'editSubResource',
    'click .edit-adhoc-charge': 'editAdhocCharge',
    'click .collapsed' : 'toggleHiddenElement',
    'click .remove-sub-resource': 'removeSubResource',
    //'keypress #discount_amount_dummy': 'addDiscountAmount',
    'keyup #discount_amount_dummy': 'addDiscountAmount',
    //'keydown #discount_amount_dummy': 'addDiscountAmount'
  },

  addSubResource: function(e){
    var ref = CRM.$(e.currentTarget).data('ref');/////////////////
    //resourceTotal[ref] = 0;
    endDate = CRM.$(e.currentTarget).data('edate');
    startDate = CRM.$(e.currentTarget).data('sdate');
    var model = new CRM.BookingApp.Entities.AddSubResource({parent_ref_id:ref, time_required:startDate});
    var view = new AddSubResource.AddSubResourceModal({model: model, is_new: true});
    view.title = ts('Add Unlimited Resource');
    CRM.BookingApp.modal.show(view);
  },

  addDiscountAmount: function(e){
    var currentSubTotal     = parseFloat(this.model.get('sub_total'));
    var currentAdhocCharges = parseFloat(this.model.get('adhoc_charges').total);
    // Get the discount amount stripping out non-numeric characters
    var sDiscountAmount      = $(e.currentTarget).val().replace(/[^\d.-]/g, '');
    var fDiscountAmount      = parseFloat(sDiscountAmount);
    if (!_.isNumber(fDiscountAmount) || _.isNaN(fDiscountAmount)) {
      fDiscountAmount = 0;
      sDiscountAmount = '';
    }
    var newTotal = (currentSubTotal + currentAdhocCharges) - fDiscountAmount;
    try{newTotal = newTotal.toFixed(2); }catch(err){}
    this.model.set("total_price", newTotal);
    this.model.set("discount_amount", sDiscountAmount);
    CRM.BookingApp.vent.trigger('render:price', this.model );
  },

  editAdhocCharge: function(e) {
    var model = new CRM.BookingApp.Entities.AdhocCharges({
        items : this.model.get('adhoc_charges').items,
        note : this.model.get('adhoc_charges').note,
        total : this.model.get('adhoc_charges').total
    });
    var view = new AddSubResource.EditAdhocChargesModal({
        model : model
    });
    view.title = ts('Edit Additional Charges');
    CRM.BookingApp.modal.show(view);
  },

  toggleHiddenElement: function(e){
    var row = $(e.currentTarget).data('ref');
    $('#crm-booking-sub-resource-row-' + row).toggle();
  },
  removeSubResource: function(e){
    var ref = $(e.currentTarget).data('ref');
    var parentRef = $(e.currentTarget).data('parent-ref');
    var price = $(e.currentTarget).data('price');
    $('#crm-booking-sub-resource-individual-row-' + ref).remove();
    delete this.model.attributes.sub_resources[ref];

    var newResourcePrice = parseFloat(this.model.get("resources")[parentRef]) - parseFloat(price);

    this.model.attributes.resources[parentRef] = newResourcePrice;
    resourceTotal[parentRef] -= parseFloat(price);
    try{resourceTotal[parentRef] = resourceTotal[parentRef].toFixed(2);}catch(err){}
    $("#resource-total-price-" + parentRef).text(resourceTotal[parentRef]);
    var currentSubTotal = this.model.get('sub_total');
    var newSubTotal = parseFloat(this.model.get('sub_total') - parseFloat(price));
    var currentTotal = this.model.get('total_price');
    var newTotal = parseFloat(currentTotal) - parseFloat(price);

    this.model.set("sub_total",  newSubTotal);
    this.model.set("total_price", newTotal);

    CRM.BookingApp.vent.trigger('render:price', this.model , parentRef );
    CRM.BookingApp.vent.trigger('update:resources', this.model);
    CRM.alert(ts(''), ts('Unlimited resource removed'), 'success');
  },

  //when edit sub resource
  editSubResource: function(e) {
    var refId = $(e.currentTarget).data('ref');   //retrieve id from attribute data-ref
    var parentRef = $(e.currentTarget).data('parent-ref');   //retrieve id from attribute data-parent-ref
    var timeRequired = $(e.currentTarget).data('time-required');  //retrieve datetime from attribute data-time-required
    selectedItem = this.model.attributes.sub_resources[refId];

    //create backbone model form json object
    var model = new CRM.BookingApp.Entities.AddSubResource({
        parent_ref_id : parentRef,
        ref_id : refId,
        resource: {id : selectedItem.resource.id, label :selectedItem.resource.label},
        configuration: selectedItem.configuration,
        quantity: selectedItem.quantity,
        time_required: timeRequired,
        note: selectedItem.note,
        price_estimate: selectedItem.price_estimate,
    });
    //create backbone view
    var view = new AddSubResource.AddSubResourceModal({
        model : model,
        is_new: false
    });
    view.title = ts('Edit unlimited resource');
    CRM.BookingApp.modal.show(view);
  }

});
// The addRegions method has been removed and not present in the
// current Marionette version. The Application.extend could be the
// solution for passing the necessary parameters to the application.
var MyApp = Marionette.Application.extend({
  region: "#resource-main",
  modal: ModalRegion,
  Utils: {
    //http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
    getCurrentUnixTimstamp:  function () {
      return unix = Math.round(+new Date()/1000);
    },
    //http://stackoverflow.com/questions/10834796/validate-that-a-string-is-a-positive-integer
    isPositiveInteger: function (n){
      return 0 === n % (!isNaN(parseFloat(n)) && 0 <= ~~n);
    },
    isPositiveNumber: function (n){
      return !_.isNumber(n) && 0 <= n;
    },
  },
  Entities: Entities,
  onStart: function(app, options) {
    this.getRegion().show(new ResourceTableView({model: new this.Entities.SubResource()}));
  },
});
CRM.BookingApp = new MyApp();

CRM.BookingApp.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});
