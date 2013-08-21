CRM.BookingApp = new Backbone.Marionette.Application();

// see http://lostechies.com/derickbailey/2012/04/17/managing-a-modal-dialog-with-backbone-and-marionette/
var ModalRegion = Backbone.Marionette.Region.extend({
  el: "#crm-booking-dialog",

  constructor: function(){
    Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
    this.on("show", this.showModal, this);
    this.on("close", this.hideModal, this);

  },

  showModal: function(view){
    view.on("close", this.hideModal, this);
    var title = '';
    if(view.template == '#add-sub-resource-template'){
      var title = ts('Add sub resource');
    }else if(view.template == '#edit-adhoc-charges-template'){
      var title = ts('Edit ad-hoc charges');
    }
    //use CiviCRM diaglog
    cj('#crm-booking-dialog').dialog({
      modal: true,
      title: title,
      minWidth: '700',
      close: function() {
        cj( this ).dialog( "destroy" );
      }
    })
  },

  hideModal: function(){
    cj('#crm-booking-dialog').dialog().dialog( "destroy" );
  }
});

CRM.BookingApp.addRegions({
  main: "#resource-main",
  modal: ModalRegion

});

CRM.BookingApp.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});

CRM.BookingApp.addInitializer(function(){
  var view = new CRM.BookingApp.AddSubResource.ResourceTableView();
  CRM.BookingApp.main.show(view);
});


