CRM.App.module('Layout', function(Layout, App, Backbone, Marionette, $, _){
  
  App.Layout = Backbone.Marionette.Layout.extend({
	  template: "#layout-template",

	  regions: {
	    searchForm: "#search-form",
	    searchResult: "#search-result"
	  }
});


});