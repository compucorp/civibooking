
/*
 * View classes belong to the second wizard screen of create/edit booking
 */
CRM.BookingApp.module('AddSubResource', function(AddSubResource, BookingApp, Backbone, Marionette, $, _) {

	CRM.BookingApp.vent.on("update:resources", function(model) {
		$('#sub_resources').val(JSON.stringify(model.toJSON()));
	}); 

	CRM.BookingApp.vent.on("render:price", function(model) {
		$("#total_price").val(model.attributes.total_price);
		$("#total-price-summary").text(model.attributes.total_price);
		$("#sub_total").val(model.attributes.sub_total);
		$("#sub-total-summary").text(model.attributes.sub_total);
		$('#adhoc_charge').val(model.attributes.adhoc_charges.total);
		$('#ad-hoc-charge-summary').html(model.attributes.adhoc_charges.total);
	});

	CRM.BookingApp.vent.on("render:options", function(options) {
		var select = options.context.$el.find(options.element);
		if (select.is('[disabled]')) {
			select.prop('disabled', false);
		}
		select.html(options.template({
			options : options.list,
			first_option : options.first_option
		}));
	}); 

  //Resource table view
  AddSubResource.ResourceTableView = Backbone.Marionette.ItemView.extend({
    template: '#resource-table-template',
    initialize: function(){
      if ($.trim($("#sub_resources").val())) {
        this.model.attributes = JSON.parse($.trim($("#sub_resources").val()));
      }
      this.model.attributes.total_price = $("#total_price").val();
      this.model.attributes.sub_total = $("#sub_total").val();
      //this.model.attributes.adhoc_charges = $("#adhoc_charge").val();
      this.model.attributes.discount_amount = $("#discount_amount").val();
    },
    onRender: function(){
      var self = this;
      //init the current price for each resource
      this.$el.find("span[id^='resource-price-']").each(function(){
        var el = $(this);
        self.model.attributes.resources[el.data('ref')] = el.text();
      });
      var items = [];
      var template = _.template($('#sub-resource-row-template').html());
      _.each(this.model.get('sub_resources'), function (item, key){
        self.$el.find("#crm-booking-sub-resource-table-" + item.parent_ref_id).append(template(item));
        items.push(item);
      });
      if($.trim($("#sub_resources").val())) {
        this.$el.find("span[id^='resource-total-price-']").each(function(){
          var el = $(this);
          var resourceTotalPrice = parseFloat(el.data('price'));
          _.find(items, function (item) {

            if(parseInt(item.parent_ref_id) === parseInt(el.data('ref'))){
              resourceTotalPrice += parseFloat(item.price_estimate);
            }
          });
          if(resourceTotalPrice != null){
            el.text(resourceTotalPrice);
            self.$el.find('#crm-booking-sub-resource-row-' + el.data('ref')).show();
          }
        });
      }
      this.$el.find("#sub-total-summary").text(this.model.get("sub_total"));
      this.$el.find("#ad-hoc-charge-summary").text(this.model.get("adhoc_charges").total);
      this.$el.find("#discount_amount").text(this.model.get("discount_amount"));
      this.$el.find("#total-price-summary").text(this.model.get("total_price"));
    },
    events: {
      'click .add-sub-resource': 'addSubResource',
      'click .edit-sub-resource': 'editSubResource',
      'click .edit-adhoc-charge': 'editAdhocCharge',
      'click .collapsed' : 'toggleHiddenElement',
      'click .remove-sub-resource': 'removeSubResource',
      'keypress #discount_amount': 'addDiscountAmount',
      'keyup #discount_amount': 'addDiscountAmount',
      'keydown #discount_amount': 'addDiscountAmount',
    },
    addSubResource: function(e){
     var ref = $(e.currentTarget).data('ref');
     var startDate =  $(e.currentTarget).data('sdate');
     var model = new CRM.BookingApp.Entities.AddSubResource({parent_ref_id:ref, time_required:startDate});
     var view = new AddSubResource.AddSubResourceModal({model: model, is_new: true});
     view.title = ts('Add unlimited resource');
     CRM.BookingApp.modal.show(view);
    },
    addDiscountAmount: function(e){
      var currentSubTotal = this.model.get('sub_total');
      var currentAdhocCharges = this.model.get('adhoc_charges');
      var discountAmount = $(e.currentTarget).val();
      var newToal = 0.0;
      if(CRM.BookingApp.Utils.isPositiveInteger(discountAmount)){
         newTotal = (parseFloat(currentSubTotal) + parseFloat(currentAdhocCharges.total))- discountAmount;
      }else{
         newTotal = (parseFloat(currentSubTotal) + parseFloat(currentAdhocCharges.total))- 0;
      }
      this.model.set("total_price", newTotal);
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

  //Sub(Unlimited) resource dialog view
  AddSubResource.AddSubResourceModal = BookingApp.Common.Views.BookingProcessModal.extend({
    template: "#add-sub-resource-template",
    initialize: function(options){
      this.isNew = options.is_new;
    },
    events: {
      'click #add-to-basket': 'addSubResource',
      'change #resource_select': 'getConfigurations',
      'change #configuration_select': 'updatePriceEstmate',
      'keypress #quantity': 'updatePriceEstmate',
      'keyup #quantity': 'updatePriceEstmate',
      'keydown #quantity': 'updatePriceEstmate',
    },
    onRender: function(){
      BookingApp.Common.Views.BookingProcessModal.prototype.onRender.apply(this, arguments);
      var thisView = this;  //set 'this' object for calling inside callback function 
      this.$el.find('#loading').show();
      var initsdate = moment(this.model.get('time_required'), "YYYY-MM-DD HH:mm:ss");
      var sTime = [initsdate.hours(), ":", initsdate.minute() < 10 ? '0' + initsdate.minute() : initsdate.minute()].join("");
      this.$el.find("#required-time-select").val(sTime); 
      this.$el.find("#required-day-select").val(initsdate.format("D"));
      this.$el.find("#required-month-select").val(initsdate.months() + 1);
      this.$el.find("#required-year-select").val(initsdate.years());

       CRM.api('Resource', 'get', {'sequential': 1, 'is_unlimited': 1, 'is_deleted': 0, 'is_active': 1},
        {success: function(data) {
            thisView.template =  _.template($('#add-sub-resource-template').html());
            //thisView.resources = data.values;
            var tpl = _.template($('#select-option-template').html());
            var params = {
                context: thisView,
                template: tpl,
                list: data.values,
                element: "#resource_select",
                first_option: ['- ', ts('select resource'), ' -'].join("")
            }
            CRM.BookingApp.vent.trigger("render:options", params);
            
            if(thisView.isNew == false){
              //set values
              thisView.$el.find("#resource_select").val(thisView.model.get('resource').id);
              //set configuration option value
              thisView.$el.find("#configuration_select").attr('data-selected-id',thisView.model.get('configuration').id);
              thisView.getConfigurations(); //call inside callback function
              thisView.$el.find("#quantity").val(thisView.model.get('quantity'));
              thisView.$el.find("#sub-resource-note").val(thisView.model.get('note'));
              thisView.$el.find("#price-estimate").html(thisView.model.get('price_estimate'));
              
              thisView.$el.find("#quantity").prop('disabled', false); //enable quantity input text
            }
            thisView.$el.find('#loading').hide();
            thisView.$el.find('#content').show();
          }
        }
      );
    },

     /**
     * Define form validation rules
     *
     * @param View view the view for which validation rules are created
     * @param Object r the validation rules for the view
     */
    onValidateRulesCreate: function(view, r) {
      _.extend(r.rules, {
        resource_select: {
          required: true
        },
        configuration_select: {
          required: true
        },
        /*time_required: {
          required: true
        },*/
        quantity: {
          required: true,
          number: true
        },
      });
    },
    //calcualte price
    updatePriceEstmate: function(e){
      var quantitySelector = this.$el.find('#quantity');
      if(e.type == 'change'){
        var configSelect = this.$el.find('#configuration_select');
        if(configSelect.val() !== ''){
          configSelect.find(':selected').data('price');
          var price = configSelect.find(':selected').data('price');
          this.model.set('configuration', {id: configSelect.val(), label:  configSelect.find(':selected').text(), price: price});
          quantitySelector.prop('disabled', false);
        }else{
          qualitySelector.prop('disabled', true);
          quantitySelector.val('');
        }
      }
      var configPrice = this.model.get('configuration').price
      var quantity = quantitySelector.val();
      if(CRM.BookingApp.Utils.isPositiveInteger(quantity)){
         var priceEstimate = quantity * configPrice;
        this.model.set('quantity', quantity);
        this.model.set('price_estimate', priceEstimate);
        this.$el.find('#price-estimate').html(priceEstimate);
      }
    },

    //render configuration options 
    getConfigurations: function(e){
      selectedVal = $('#resource_select').val();
      if(selectedVal !== ""){
        var params = {
              id: selectedVal,
              sequential: 1,
              'api.resource_config_set.get': {
                id: '$value.set_id',
                'api.resource_config_option.get': {
                  set_id: '$value.id',
                  'api.option_group.get':{
                    name: 'booking_size_unit',
                  },
                  'api.option_value.get':{
                    value: '$value.unit_id',
                    option_group_id: '$value.api.option_group.get.id'
                  }
                }
              }
            };
        this.$el.find('#config-loading').show();
        this.$el.find('#configuration_select').hide();
        var self = this;
        CRM.api('Resource', 'get', params,
          { context: self,
            success: function(data) {
            var resource =  data['values']['0'];
            var options = data['values']['0']['api.resource_config_set.get']['values']['0']['api.resource_config_option.get']['values'];
            self.model.set('resource', {id: resource.id, label: resource.label});
            var params = {
              context: self,
              template: _.template($('#select-config-option-template').html()),
              list: options,
              element: "#configuration_select",
              first_option: '- ' + ts('select configuration') + ' -'
            }
            CRM.BookingApp.vent.trigger("render:options", params);
            //set configuration options for edit mode of subresource view
            var configSelectedId = this.$el.find('#configuration_select').data('selected-id');  //retrieve data from data-selected-id attribute
            if(configSelectedId != 'undefined'){
              this.$el.find('#configuration_select').val(configSelectedId);
            }
            this.$el.find('#config-loading').hide();
            this.$el.find('#configuration_select').show();
          }
        });
      }else{
        var params = {
          context:this,
          template: _.template($('#select-config-option-template').html()),
          list: new Array(),
          element: "#configuration_select",
          first_option: '- ' + ts('select configuration') + ' -'}
        CRM.BookingApp.vent.trigger("render:options", params);
        this.$el.find('#configuration_select').prop('disabled', true);
      }
    },
    
    //save sub-resource
    addSubResource: function(e){
      e.preventDefault();
      if (!this.$('form').valid()) {
        var errors = this.$('form').validate().errors();
        this.onRenderError(errors);
        return false;
      }
      this.model.set('note', this.$el.find('#sub-resource-note').val());
      var requiredTime = this.$el.find("#required-time-select").val().split(":");
      var requiredDate = new Date(
        this.$el.find("#required-year-select").val(),
        this.$el.find("#required-month-select").val() - 1,
        this.$el.find("#required-day-select").val(),
        requiredTime[0],
        requiredTime[1]
      );
			var timeRequired = moment(requiredDate).format("YYYY-M-D HH:mm");
			this.model.set('time_required', timeRequired); 

      var parentRefId = this.model.get('parent_ref_id');
      
      var refId = null;
      if(this.isNew){
        refId = CRM.BookingApp.Utils.getCurrentUnixTimstamp();
        this.model.set('ref_id', refId);
      }else{
        refId = this.model.get('ref_id');
      }
        
      var template = _.template($('#sub-resource-row-template').html());
      
      //ui update
      if(this.isNew){
        $('#crm-booking-sub-resource-table-' + parentRefId).find('tbody').append(template(this.model.toJSON()));
      }else{
        $('#crm-booking-sub-resource-individual-row-'+refId).replaceWith(template(this.model.toJSON()));
      }
      $('#crm-booking-sub-resource-row-' + parentRefId).show();
      var resourceRefId = this.model.get("parent_ref_id");
      var priceEstimate = this.model.get("price_estimate");

      var subResourceModel = CRM.BookingApp.main.currentView.model;
      subResourceModel.attributes.sub_resources[refId] = this.model.toJSON();

      var currentSubTotal = subResourceModel.get('sub_total');
      var newSubTotal = parseFloat(priceEstimate) + parseFloat(subResourceModel.get('sub_total'));
      subResourceModel.set("sub_total",  newSubTotal);

      var currentTotal = subResourceModel.get('total_price');
      var newTotal = (parseFloat(currentTotal) - parseFloat(currentSubTotal)) + parseFloat(newSubTotal);
      subResourceModel.set("total_price", newTotal);

      var currentResourceTotal = subResourceModel.get("resources")[resourceRefId];

      var resourceTotalPrice = parseFloat(currentResourceTotal) + parseFloat(priceEstimate);
      subResourceModel.attributes.resources[resourceRefId] = resourceTotalPrice;
      //set total price for resource tow
      $("#resource-total-price-" + resourceRefId).text(subResourceModel.attributes.resources[resourceRefId]);
      CRM.BookingApp.vent.trigger('render:price', subResourceModel, resourceRefId );
      CRM.BookingApp.vent.trigger('update:resources', subResourceModel);
      CRM.BookingApp.modal.close(this);
    },

  });

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
      this.$el.find('#total-adhoc-charges').html(total);
      this.model.set('total', total);
    },
    updateAdhocCharges: function(e){
      e.preventDefault();
      var subResourceModel = CRM.BookingApp.main.currentView.model;
      this.model.set('note',this.$el.find('#adhoc-charges-note').val() );
      var adhocChargesTotal = this.model.get('total');
      //console.log(adhocChargesTotal);
      subResourceModel.set('adhoc_charges', this.model.attributes);
      var currentTotal = subResourceModel.get('total_price');
      var newTotal = parseFloat(adhocChargesTotal) + parseFloat(currentTotal);
      subResourceModel.set("total_price", parseFloat(newTotal));
      CRM.BookingApp.vent.trigger('render:price', subResourceModel);
      CRM.BookingApp.vent.trigger('update:resources', subResourceModel);
      //console.log(subResourceModel.attributes);
      CRM.BookingApp.modal.close(this);
    }

  });

});
