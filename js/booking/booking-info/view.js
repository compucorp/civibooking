var BookingInfoViews = {
  Dialog: Marionette.View.extend({
    template: CRM._.template(CRM.$('#booking-info-profile-template').html()),
    initialize: function(options){
      this.title = options.title;
      this.profile = options.profile;
      this.elementId = options.elementId;
      this.targetElementId = options.targetElementId;
    },
    onRender: function(){
      var self = this;
      self.profileUrl = CRM.url('civicrm/profile/create', {
            reset: 1,
            snippet: 6,
            gid: self.profile
      });
      this.$el.find('#crm-booking-profile-form').dialog({
        title: self.title,
        modal: true,
        minWidth: 600,
          open: function() {
            CRM.$.getJSON(self.profileUrl, function(data) {
              self.displayNewContactProfile(data);
            });
          },
          close: function() {
            CRM.$(this).dialog('destroy');
          }
      });
    },
    displayNewContactProfile: function(data) {
      var self = this;
      CRM.$('#crm-booking-profile-form').html(data.content);
      CRM.$("#crm-booking-profile-form .cancel.form-submit").click(function() {
        CRM.$("#crm-booking-profile-form").dialog('close');
        return false;
      });
      CRM.$('#email-Primary').addClass('email');
      CRM.$("#crm-booking-profile-form form").ajaxForm({
        // context=dialog triggers civi's profile to respond with json instead of an html redirect
        // but it also results in lots of unwanted scripts being added to the form snippet, so we
        // add it here during submission and not during form retrieval.
        url: self.profileUrl + '&context=dialog',
        dataType: 'json',
        success: function(response) {
          if (response.newContactSuccess) {
            CRM.$('#crm-booking-profile-form').dialog('close');
            CRM.alert(ts('%1 has been created.', {1: response.displayName}), ts('Contact Saved'), 'success');
            console.log('test',self.targetElementId);
            CRM.$('input[name="'+self.targetElementId+'"]').val(response.contactID);
            CRM.$(self.elementId).val(response.displayName);
          }
          else {
            self.displayNewContactProfile(response);
          }
        }
      }).validate(CRM.validate.params);
    }

  }),
};
