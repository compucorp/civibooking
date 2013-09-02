CRM.BookingApp.module('Common.Views', function(Views, BookingApp, Backbone, Marionette, $, _) {


  /**
   * A form that use in a Modal that required the validate in the form
   *
   */
  Views.BookingProcessModal = Marionette.ItemView.extend({
    onRender: function() {
      var rules = this.createValidationRules();
      this.$('form').validate(rules);
    },
    /**
     *
     * @return {*} jQuery.validate rules
     */
    createValidationRules: function() {
      var rules = _.extend({}, CRM.validate.params);
      rules.rules || (rules.rules = {});
      this.triggerMethod("validateRules:create", this, rules);
      return rules;
    },

    onRenderError: function(errors){
      var view = this;
      _.each(errors, function(error) {
        console.log($(error).attr('for'));
         view.$('[name=' + $(error).attr('for') + ']').crmError($(error).text());
      });
    }

  });


});
