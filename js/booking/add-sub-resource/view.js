CRM.BookingApp.module('AddSubResource', function(AddSubResource, Booking, Backbone, Marionette, $, _) {

  AddSubResource.ResourceTableView = Backbone.Marionette.ItemView.extend({
    template: '#resource-table-template',
    events: {
      'click .add-sub-resource': 'addSubResource',
      'click .edit-adhoc-charge': 'editAdhocCharge',
      'click .collapsed' : 'toggleHiddenElement',
    },
    addSubResource: function(e){
     var ref = $(e.currentTarget).data('ref');
     var date = $(e.currentTarget).data('date');
     //var model = new ResourceSearch.Model.Basket({id: ref, date: date});
     var view = new AddSubResource.AddSubResourceModal();
     CRM.BookingApp.modal.show(view);

    },
    editAdhocCharge: function(e){
      var view = new AddSubResource.EditAdhocChargesModal();
     CRM.BookingApp.modal.show(view);
    },
    toggleHiddenElement: function(e){
      var row = $(e.currentTarget).data('ref');
      $('#crm-booking-sub-resource-row-' + row).toggle();

    }
  });

  AddSubResource.AddSubResourceModal = Backbone.Marionette.ItemView.extend({
    template: "#add-sub-resource-template",
    //className: "modal-dialog",
    events: {
      'click #add-to-basket': 'addSubResource',
      'click .cancel': 'addSubResource',
    },
    addSubResource: function(e){
     //CRM.BookingApp.modal.close(this);
    }


  });

  AddSubResource.EditAdhocChargesModal = Backbone.Marionette.ItemView.extend({
    template: "#edit-adhoc-charges-template",
    className: "modal-dialog",
    events: {
    },


  });



});
