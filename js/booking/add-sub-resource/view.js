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

	CRM.BookingApp.vent.on("update:resources", function(model) {
		$('#sub_resources').val(JSON.stringify(model.toJSON()));
	});

	CRM.BookingApp.vent.on("render:price", function(model) {
		$("#total_price").val(model.attributes.total_price);
		var totalText = model.attributes.total_price;
		try{
		  if(model.attributes.total_price>=0){
		    var totalText = model.attributes.total_price.toFixed(2);
		  }
		}catch(err){}
    $("#total-price-summary").text(totalText);

		$("#discount_amount").val(model.attributes.discount_amount);
    $('#discount_amount_dummy').val(model.attributes.discount_amount);

		$("#sub_total").val(model.attributes.sub_total);
		var subtotalText = model.attributes.sub_total;
		try{
        var subtotalText = model.attributes.sub_total.toFixed(2);
    }catch(err){}
		$("#sub-total-summary").text(subtotalText);

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
      var timeTxt = [initsdate.hours() < 10 ? '0' + initsdate.hours() : initsdate.hours(), ":", initsdate.minute() < 10 ? '0' + initsdate.minute() : initsdate.minute()].join("");

      //set the formatted months
      var month=new Array();
      month[0]="01";
      month[1]="02";
      month[2]="03";
      month[3]="04";
      month[4]="05";
      month[5]="06";
      month[6]="07";
      month[7]="08";
      month[8]="09";
      month[9]="10";
      month[10]="11";
      month[11]="12";
      var dateTxt = [ initsdate.format("DD"),"/", month[initsdate.months()],"/", initsdate.years()].join("");
      this.$el.find("#required_date").val(dateTxt);
      this.$el.find("#required_time").val(timeTxt);

       CRM.api('Resource', 'get', {'sequential': 1, 'is_unlimited': 1, 'is_deleted': 0, 'is_active': 1},
        {success: function(data) {
            thisView.template =  _.template($('#add-sub-resource-template').html());
            //var configValue = CRM_Booking_BAO_BookingConfig::getConfig();

            thisView.$el.find("#required_date").datepicker({changeMonth: true, changeYear: true, dateFormat: 'dd/mm/yy'});
            thisView.$el.find('#required_time').timeEntry({show24Hours: true}).change(function() {
              var log = $('#log');
              log.val(log.val() + ($('#defaultEntry').val() || 'blank') + '\n');
            });

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

    beforeClose: function() {this.$('form').find("#required_date").datepicker("destroy");},

     /**
     * Define form validation rules
     *
     * @param View view the view for which validation rules are created
     * @param Object r the validation rules for the view
     */
    onValidateRulesCreate: function(view, r) {
        $.validator.addMethod("withinValidTime", function(value, element) {
        var dateVals = $("#required_date").val().split("/");
        var timeVals = $("#required_time").val().split(":");
        var requiredDate = new Date(dateVals[2],dateVals[1]-1,dateVals[0],timeVals[0],timeVals[1]);
        var minDate = moment(startDate, "YYYY-MM-DD HH:mm:ss");
        var maxDate = moment(endDate, "YYYY-MM-DD HH:mm:ss");
        if (unlimitedTimeConfig==0){
          return true;
        }else{
          var val = requiredDate>=minDate && requiredDate<=maxDate;
          return val;
        }
        }, ts("Please select the date and time during the valid booking time."));
     _.extend(r.rules, {
        resource_select: {
          required: true
        },
        configuration_select: {
          required: true
        },
        "required_date": {
          required: true,
            "withinValidTime": true
        },
        quantity: {
          required: true,
          digits: true
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
        this.model.set('price_estimate', priceEstimate.toFixed(2));
        this.$el.find('#price-estimate').html(priceEstimate.toFixed(2));
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
                is_active: 1,
                is_deleted: 0,
                'api.resource_config_option.get': {
                  set_id: '$value.id',
                  is_active: 1,
                  is_deleted: 0,
                  'api.option_group.get':{
                    name: 'booking_size_unit',
                  },
                  'api.option_value.get':{
                    value: '$value.unit_id',
                    sequential: 1,
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
            var configSet = data['values']['0']['api.resource_config_set.get'];            
            if(configSet.count !== 1){
              var url = CRM.url('civicrm/admin/resource/config_set', {
                reset: 1,
                action: 'update',
                id: resource.id
              });
              CRM.alert(ts(''), ts('Your resource configuration set is disabled.') 
                      + ' ' + ' <a href="' + 
                      url + '">' + ts('Click here to edit configuration set.') + '</a> ', 'error');
            }
            else if(configSet['values']['0']['api.resource_config_option.get'].count < 1){
              var url = CRM.url('civicrm/admin/resource/config_set/config_option', {
                reset: 1,
                sid: configSet['values']['0'].id
              });
              CRM.alert(ts(''), ts('Your resource configuration options are all disabled or none have been created.') 
                      + ' ' + ' <a href="' + 
                      url + '">' + ts('Click here to edit or create options.') + '</a> ', 'error');
            }
            else{
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
      this.$('form').find("#required_date").datepicker("destroy");
      this.model.set('note', this.$el.find('#sub-resource-note').val());
      var dateVals = this.$el.find("#required_date").val().split("/");
      var timeVals = this.$el.find("#required_time").val().split(":");
      var requiredDate = new Date(dateVals[2],dateVals[1]-1,dateVals[0],timeVals[0],timeVals[1]);
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
      var subResourceRefId = this.model.get("ref_id");

      var subResourceModel = CRM.BookingApp.main.currentView.model;
      subResourceModel.attributes.sub_resources[refId] = this.model.toJSON();

      var currentSubTotal = subResourceModel.get('sub_total');
      var newSubTotal = parseFloat(priceEstimate) + parseFloat(subResourceModel.get('sub_total'));
      subResourceModel.set("sub_total",  newSubTotal);

      var currentTotal = subResourceModel.get('total_price');
      var newTotal = (parseFloat(currentTotal) - parseFloat(currentSubTotal)) + parseFloat(newSubTotal);
      subResourceModel.set("total_price", newTotal);

      var currentResourceTotal = resourceTotal[resourceRefId];
      
      
      if(this.isNew){
        var resourceTotalPrice = parseFloat(currentResourceTotal) + parseFloat(priceEstimate);
        priceCache[subResourceRefId] = parseFloat(priceEstimate);
      }else{
        var resourceTotalPrice = parseFloat(currentResourceTotal) + parseFloat(priceEstimate) - priceCache[subResourceRefId];
        subResourceModel.attributes.sub_total = subResourceModel.attributes.sub_total - priceCache[subResourceRefId];
        subResourceModel.attributes.total_price = subResourceModel.attributes.total_price - priceCache[subResourceRefId];
        priceCache[subResourceRefId] = parseFloat(priceEstimate);
      }
      resourceTotal[resourceRefId] = resourceTotalPrice;
      subResourceModel.attributes.resources[resourceRefId] = resourceTotalPrice.toFixed(2);
      //set total price for resource row
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
