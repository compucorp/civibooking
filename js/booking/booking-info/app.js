CRM.BookingApp = new Backbone.Marionette.Application();

var ModalRegion = Backbone.Marionette.Region.extend({
  el: "#crm-booking-profile-form",

  constructor: function(){
    Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
    this.on("show", this.showModal, this);
    this.on("close", this.hideModal, this);

  },

  showModal: function(view){
   // view.on("close", this.hideModal, this);


  },

  hideModal: function(){
    cj('#crm-booking-dialog').dialog().dialog( "destroy" );
  }
});

CRM.BookingApp.addRegions({
  contactRegion: "#contact-container",
  orgRegion: "#organisation-container",
  modal: ModalRegion

});

CRM.BookingApp.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});

CRM.BookingApp.addInitializer(function(){

});


