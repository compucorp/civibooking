CRM.ResourceSearch.module('View', function(View, ResourceSearch, Backbone, Marionette, $, _) {

  View.SearchForm = Backbone.Marionette.ItemView.extend({
    template: '#search-form-template',

    events: {
    	"click #searchButton": "search",
    },

    search: function(e){
  		var resourceId = this.$el.find("#resource_id").val();
  		var resourceType = this.$el.find("#resource_type").val();
      var queryString = '';
      //FIXME: Make the query string look better
      queryString += 'rid=' + resourceId;
      queryString += '&type=' + resourceType;
      ResourceSearch.vent.trigger("search:query", queryString);
      e.preventDefault();

    }

  });

  View.AddToBasket = Backbone.Marionette.ItemView.extend({
    template: "#add-to-basket-form-template",
    className: "modal-dialog",
    events: {
      "click #addToBasket": "addToBasket",
    },
    addToBasket: function(){

    }

  });

  
  View.ResourceRow = Backbone.Marionette.ItemView.extend({
    template: "#search-result-row-template",
    tagName: "tr",
    className: "slots",
    initialize: function(){},
    events: {
      'click .add-to-basket': 'loadForm'
    },
    loadForm: function(){
     var view = new View.AddToBasket();
     ResourceSearch.modal.show(view);
  
    }
   
  });


  View.ResourceTable = Backbone.Marionette.CompositeView.extend({
    template: '#search-result-table-template',
    itemViewContainer: "tbody",
    itemView: View.ResourceRow,
  
    initialize: function(){
      this.id = this.model.get('result').date_timestamp;
      var resources = [];
      _.each(this.model.get('result').resources, function (resource){
        resources[resources.length] = new ResourceSearch.Model.Resource({
          id: resource.id,
          description: resource.description,
          is_unlimited: resource.is_unlimited,
          label: resource.label,
          resource_location: resource.resource_location,
          resource_type: resource.resource_type,
          slots: resource.slots,
        });
      });
      this.collection = new ResourceSearch.Collection.ResourceList(resources);
    },
    
    
    appendHtml: function(collectionView, itemView){  
      collectionView.$("#" + collectionView.id + " > tbody").append(itemView.el);
    },
    
    events: {

    },
    

  });

  View.ResourceCollection = Backbone.Marionette.CollectionView.extend({
    itemView: View.ResourceTable,
    events: {},
    initialize: function(){}
  });



});
