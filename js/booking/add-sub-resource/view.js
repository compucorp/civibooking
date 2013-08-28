CRM.BookingApp.module('AddSubResource', function(AddSubResource, BookingApp, Backbone, Marionette, $, _) {

   CRM.BookingApp.vent.on("update:resources", function (model){
    $('#resources').val(JSON.stringify(model.toJSON()));
   });

  CRM.BookingApp.vent.on("render:options", function (options){
    var select = options.context.$el.find(options.element);
    if(select.is('[disabled]')){
      select.prop('disabled', false);
    }
    select.html(options.template({options: options.list, first_option: options.first_option}));
  });

  AddSubResource.ResourceTableView = Backbone.Marionette.ItemView.extend({
    template: '#resource-table-template',
    initialize: function(){
      if ($.trim($("#resources").val())) {
        this.model.attributes = JSON.parse($.trim($("#resources").val()));
      }
    },
    onRender: function(){
      var self = this;
      var template = _.template($('#sub-resource-row-template').html());
      _.each(this.model.get('sub_resources'), function (item, key){
        self.$el.find("#crm-booking-sub-resource-table-" + item.parent_ref_id).append(template(item));
      });

    },
    events: {
      'click .add-sub-resource': 'addSubResource',
      'click .edit-adhoc-charge': 'editAdhocCharge',
      'click .collapsed' : 'toggleHiddenElement',
      'click .remove-sub-resource': 'removeSubResource',
    },
    addSubResource: function(e){
     var ref = $(e.currentTarget).data('ref');
     CRM.api('Resource', 'get', {'sequential': 1, ' is_unlimited': 1, 'is_deleted': 0, 'is_active': 1},
      {success: function(data) {
          var model = new CRM.BookingApp.Entities.AddSubResource({parent_ref_id:ref});
          var view = new AddSubResource.AddSubResourceModal({model: model, resources: data.values});
          view.title = ts('Add sub resources');
          CRM.BookingApp.modal.show(view);
        }
      }
    );
    },
    editAdhocCharge: function(e){
      var view = new AddSubResource.EditAdhocChargesModal();
      view.title = ts('Edit ad-hoc charges');
      CRM.BookingApp.modal.show(view);
    },
    toggleHiddenElement: function(e){
      var row = $(e.currentTarget).data('ref');
      $('#crm-booking-sub-resource-row-' + row).toggle();
    },
    removeSubResource: function(e){
      var ref = $(e.currentTarget).data('ref');
      $('#crm-booking-sub-resource-individual-row-' + ref).remove();
      delete this.model.attributes.sub_resources[ref];
      CRM.BookingApp.vent.trigger('update:resources', this.model);

    }
  });

  AddSubResource.AddSubResourceModal = Backbone.Marionette.ItemView.extend({
    template: "#add-sub-resource-template",
    initialize: function(options){
      this.resources = options.resources;
    },
    events: {
      'click #add-to-basket': 'addSubResource',
      'change #resourceSelect': 'getConfigurations',
      'change #configSelect': 'updatePriceEstmate',
      'keypress #quantity': 'updatePriceEstmate',
      'keyup #quantity': 'updatePriceEstmate',
      'keydown #quantity': 'updatePriceEstmate',

    },
    onRender: function(){
     var tpl = _.template($('#select-option-template').html());
      var params = {
        context:this,
        template: tpl,
        list:this.resources,
        element: "#resourceSelect",
        first_option: ['- ', ts('select resource'), ' -'].join("");
      }
      CRM.BookingApp.vent.trigger("render:options", params);

    },
    updatePriceEstmate: function(e){
      var qualitySelector = this.$el.find('#quantity');
      if(e.type == 'change'){
        var configSelect = this.$el.find('#configSelect');
        if(configSelect.val() !== ''){
          configSelect.find(':selected').data('price');
          var price = configSelect.find(':selected').data('price');
          this.model.set('configuration', {id: configSelect.val(), label:  configSelect.find(':selected').text(), price: price});
          qualitySelector.prop('disabled', false);
        }else{
          qualitySelector.prop('disabled', true);
          qualitySelector.val('');
        }
      }
      var configPrice = this.model.get('configuration').price
      var quantity = qualitySelector.val();
      var priceEstimate = quantity * configPrice;
      this.model.set('quantity', quantity);
      this.model.set('price_estimate', priceEstimate);
      this.$el.find('#price-estimate').html(priceEstimate);
    },
    getConfigurations: function(e){
      selectedVal = $('#resourceSelect').val();
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
        var self = this;
        CRM.api('Resource', 'get', params,
          { context: self,
            success: function(data) {
            console.log(data);
            var resource =  data['values']['0'];
            var options = data['values']['0']['api.resource_config_set.get']['values']['0']['api.resource_config_option.get']['values'];
            self.model.set('resource', {id: resource.id, label: resource.label});
            var params = {
              context:self,
              template: _.template($('#select-config-option-template').html()),
              list: options,
              element: "#configSelect",
              first_option: '- ' + ts('select configuration') + ' -'
            }
            CRM.BookingApp.vent.trigger("render:options", params);

          }
        });
      }else{
        var params = {
          context:this,
          template: _.template($('#select-config-option-template').html()),
          list: new Array(),
          element: "#configSelect",
          first_option: '- ' + ts('select configuration') + ' -'}
        CRM.BookingApp.vent.trigger("render:options", params);
        this.$el.find('#configSelect').prop('disabled', true);
      }
    },
    addSubResource: function(e){
      var parentRefId = this.model.get('parent_ref_id');
      var refId = CRM.BookingApp.Utils.getCurrentUnixTimstamp();
      this.model.set('ref_id', refId);
      var template = _.template($('#sub-resource-row-template').html());
      $('#crm-booking-sub-resource-table-' + parentRefId).find('tbody').append(template(this.model.toJSON()));
      $('#crm-booking-sub-resource-row-' + parentRefId).show();
      var subResourceModel = CRM.BookingApp.main.currentView.model;
      subResourceModel.attributes.sub_resources[refId] = this.model.toJSON();
      CRM.BookingApp.vent.trigger('update:resources', subResourceModel);
      //$('#resources').val(JSON.stringify(subResourceModel.toJSON()));
      CRM.BookingApp.modal.close(this);
    }


  });

  AddSubResource.EditAdhocChargesModal = Backbone.Marionette.ItemView.extend({
    template: "#edit-adhoc-charges-template",
    className: "modal-dialog",
    events: {
  },


  });



});
