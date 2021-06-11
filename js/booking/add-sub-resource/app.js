// see http://lostechies.com/derickbailey/2012/04/17/managing-a-modal-dialog-with-backbone-and-marionette/
var ModalRegion = Marionette.Region.extend({
  el: "#crm-booking-dialog",

  constructor: function(){
    Marionette.Region.prototype.constructor.apply(this, arguments);
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
var ResourceTotal = new Array();
var PriceCache = new Array();
// The addRegions method has been removed and not present in the
// current Marionette version. The Application.extend could be the
// solution for passing the necessary parameters to the application.
var MyApp = Marionette.Application.extend({
  region: "#resource-main",
  modal: new ModalRegion(),
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
    this.getRegion().show(new Views.ResourceTableView({model: new this.Entities.SubResource()}));
  },
});
CRM.BookingApp = new MyApp();

CRM.BookingApp.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});
