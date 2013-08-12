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

  View.Basket = Backbone.Marionette.ItemView.extend({
    template: "#basket-table-template",
 
    initialize: function(){},
    events: {
     
    },
   
  })

  View.AddToBasketModal = Backbone.Marionette.ItemView.extend({
    template: "#add-to-basket-form-template",
    className: "modal-dialog",
    events: {
      "click #addToBasket": "addToBasket",
    },
    addToBasket: function(e){
      ResourceSearch.modal.reset();
      var view = new View.Basket();
      ResourceSearch.basket.show(view);
      //var select = $("select[name='resources']");
      //append object to multiselect
      //console.log($('#hiddenValue'));
      //$('#hiddenValue').append('<input name="resources[]" type="text" id="resources" class="form-text value="yeah">');
      /*

      $('#resources').append($('<option>', { 
        value: 'value',
        text : 'value',
        selected: true, 
      }));*/
      $('#resources').append('yeah\n');
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
    loadForm: function(e){
     var ref = $(e.currentTarget).data('ref');
     var date = $(e.currentTarget).data('date');
     var model = new ResourceSearch.Model.Basket({id: ref, date: date});
     var view = new View.AddToBasketModal();
     ResourceSearch.modal.show(view);
  
    }
   
  });


  View.ResourceTable = Backbone.Marionette.CompositeView.extend({
    template: '#search-result-table-template',
    //itemViewContainer: "tbody",
    itemView: View.ResourceRow,
  
    initialize: function(){
      //set view id
      this.id = this.model.get('result').date_timestamp;
      var date = this.model.get('result').date;
      var resources = [];
      _.each(this.model.get('result').resources, function (resource){
        resources[resources.length] = new ResourceSearch.Model.ResourceRow({
          id: resource.id,
          description: resource.description,
          is_unlimited: resource.is_unlimited,
          label: resource.label,
          resource_location: resource.resource_location,
          resource_type: resource.resource_type,
          date: date, //added date to a row so we know that date is seleted.
          slots: resource.slots,
        });
      });
      this.collection = new ResourceSearch.Collection.ResourceTableList(resources);
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
