CRM.BookingApp = new Backbone.Marionette.Application();

// see http://lostechies.com/derickbailey/2012/04/17/managing-a-modal-dialog-with-backbone-and-marionette/
var ModalRegion = Backbone.Marionette.Region.extend({
  el: "#crm-booking-dialog",

  constructor: function(){
    Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
    this.on("show", this.showModal, this);
    this.on("close", this.hideModal, this);

  },

});

CRM.BookingApp.addRegions({
  main: "#booking-detail-container",
  modal: ModalRegion

});

CRM.BookingApp.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});

CRM.BookingApp.addInitializer(function(){
  var view = new CRM.BookingApp.BookingInfo.FormDetail({model: new Backbone.Model});
  CRM.BookingApp.main.show(view);
});


