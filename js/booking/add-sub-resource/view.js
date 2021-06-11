(function ($, ts){ 

/*
 * View classes belong to the second wizard screen of create/edit booking
 */
CRM.BookingApp.module('AddSubResource', function(AddSubResource, BookingApp, Backbone, Marionette, $, _) {

  var startDate;
  var endDate;
  var unlimitedTimeConfig;
  var resourceTotal = new Array(); 
  var priceCache = new Array();

  //Additaional charges dialog view
  AddSubResource.EditAdhocChargesModal = BookingApp.Common.Views.BookingProcessModal.extend({
    template: "#edit-adhoc-charges-template",
    className: "modal-dialog",
    onRender: function(){
      var thisView = this;
      _.each(this.model.get('items'), function(item){
        thisView.$el.find('#' + item.name).html(item.item_price);
        thisView.$el.find('input[name="' + item.name + '"]').val(item.quantity);
      });
      this.$el.find('#adhoc-charges-note').val(this.model.get('note'));
      this.$el.find('#total-adhoc-charges').html(this.model.get('total'));
      BookingApp.Common.Views.BookingProcessModal.prototype.onRender.apply(this, arguments);
    },
    events: {
      'keypress .item': 'updatePrice',
      'keyup .item': 'updatePrice',
      'keydown .item': 'updatePrice',
      'click #update-adhoc-charges': 'updateAdhocCharges',
    },
    updatePrice: function(e){
      var el = $(e.currentTarget);
      var itemId = el.data('id');
      var price = el.data('price');
      var quantity = el.val();
      var name = el.attr('name');
      if(CRM.BookingApp.Utils.isPositiveInteger(quantity)){
        var itemPrice = parseFloat(price) * parseFloat(quantity);
        this.$el.find('#'+ name).html(parseFloat(itemPrice).toFixed(2));
        var item = {item_id: itemId, name: name, price: price, quantity: quantity, item_price: itemPrice}
        this.model.attributes.items[itemId] = item;
      }else{
        this.$el.find('#'+ name).html(0);
        this.$el.find('input[name="'+ name + '"]').val('');
         delete this.model.attributes.items[itemId];
      }
      var items = this.model.get('items');
      var total = 0.0;
      _.each(items,function(item){
       total = parseFloat(total) +  parseFloat(item.item_price);
      });
      this.$el.find('#total-adhoc-charges').html(total.toFixed(2));
      this.model.set('total', total.toFixed(2));
    },
    updateAdhocCharges: function(e){
      e.preventDefault();
      var subResourceModel = CRM.BookingApp.main.currentView.model;
      this.model.set('note',this.$el.find('#adhoc-charges-note').val() );
      var adhocChargesTotal = this.model.get('total');
      //console.log(adhocChargesTotal);
      subResourceModel.set('adhoc_charges', this.model.attributes);
      var currentTotal = subResourceModel.get('sub_total');
      var discountAmount = subResourceModel.get('discount_amount');
      if(CRM.BookingApp.Utils.isPositiveNumber(discountAmount)){
        var newTotal = (parseFloat(adhocChargesTotal) + parseFloat(currentTotal)) - parseFloat(discountAmount);
      }else{
        var newTotal = (parseFloat(adhocChargesTotal) + parseFloat(currentTotal)) - 0;
      }
      subResourceModel.set("total_price", parseFloat(newTotal).toFixed(2));
      CRM.BookingApp.vent.trigger('render:price', subResourceModel);
      CRM.BookingApp.vent.trigger('update:resources', subResourceModel);
      //console.log(subResourceModel.attributes);
      CRM.BookingApp.modal.close(this);
    }

  });

});
}(CRM.$, CRM.ts('uk.co.compucorp.civicrm.booking')));
