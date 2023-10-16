var ModalRegion = Marionette.Region.extend({
  el: "#crm-booking-profile-form",

  constructor: function(){
    Marionette.Region.prototype.constructor.apply(this, arguments);
    this.on("show", this.showModal, this);
    this.on("close", this.hideModal, this);

  },

  showModal: function(view){
  },

  hideModal: function(){
    cj('#crm-booking-dialog').dialog().dialog( "destroy" );
  }
});
var MyApp = Marionette.Application.extend({
  contactRegion: "#contact-container",
  orgRegion: "#organisation-container",
  modal: new ModalRegion(),
  onRenderDialog: function(profile, title, elementId, targetElementId){
    var view = new BookingInfoViews.Dialog({
     profile: profile,
     title: title,
     elementId: elementId,
     targetElementId: targetElementId,
    });
    CRM.BookingApp.modal.show(view);
  },
});

CRM.BookingApp = new MyApp();

CRM.BookingApp.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});
