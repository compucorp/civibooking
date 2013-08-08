CRM.ResourceSearch = new Backbone.Marionette.Application();

// see http://lostechies.com/derickbailey/2012/04/17/managing-a-modal-dialog-with-backbone-and-marionette/
var ModalRegion = Backbone.Marionette.Region.extend({
  el: "#modal",

  constructor: function(){
    Backbone.Marionette.Region.prototype.constructor.apply(this, arguments);
    this.on("show", this.showModal, this);
    this.on("close", this.hideModal, this);

  },
 
  showModal: function(view){
    view.on("close", this.hideModal, this);
    this.$el.modal('show');
  },

  hideModal: function(){
    this.$el.modal('hide');
  }
});

CRM.ResourceSearch.addRegions({
  searchForm: "#search-form",
  searchResult: "#search-result",
  modal: ModalRegion

});

CRM.ResourceSearch.on("initialize:after", function(){
  if( ! Backbone.History.started) Backbone.history.start();
});


