(function ($, ts){ 
CRM.BookingApp.module('BookingInfo', function(BookingInfo, BookingApp, Backbone, Marionette, $, _) {

  

  CRM.BookingApp.vent.on("render:dialog", function (profile, title, elementId, targetElementId){
      var view = new BookingInfo.Dialog({
       profile: profile,
       title: title,
       elementId: elementId,
       targetElementId: targetElementId});
      CRM.BookingApp.modal.show(view);
  });


  BookingInfo.Dialog = Backbone.Marionette.ItemView.extend({
    template: '#booking-info-profile-template',
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
            $.getJSON(self.profileUrl, function(data) {
              self.displayNewContactProfile(data);
            });
          },
          close: function() {
            $(this).dialog('destroy');
          }
      });
    },

    displayNewContactProfile: function(data) {
      var self = this;
      $('#crm-booking-profile-form').html(data.content);
      $("#crm-booking-profile-form .cancel.form-submit").click(function() {
      $("#crm-booking-profile-form").dialog('close');
          return false;
        });
      $('#email-Primary').addClass('email');
      $("#crm-booking-profile-form form").ajaxForm({
        // context=dialog triggers civi's profile to respond with json instead of an html redirect
        // but it also results in lots of unwanted scripts being added to the form snippet, so we
        // add it here during submission and not during form retrieval.
        url: self.profileUrl + '&context=dialog',
        dataType: 'json',
        success: function(response) {
          if (response.newContactSuccess) {
            $('#crm-booking-profile-form').dialog('close');
            CRM.alert(ts('%1 has been created.', {1: response.displayName}), ts('Contact Saved'), 'success');
            console.log('test',self.targetElementId);
            $('input[name="'+self.targetElementId+'"]').val(response.contactID);
            $(self.elementId).val(response.displayName);
          }
          else {
            self.displayNewContactProfile(response);
          }
        }
      }).validate(CRM.validate.params);
    }

  });

});
}(CRM.$, CRM.ts('uk.co.compucorp.civicrm.booking')));
