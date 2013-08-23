CRM.BookingApp.module('BookingInfo', function(BookingInfo, BookingApp, Backbone, Marionette, $, _) {

  CRM.BookingApp.vent.on("init:autocomplete", function (element, targetElement, el){
    var contactUrl = CRM.url('civicrm/ajax/rest', 'className=CRM_Booking_Page_AJAX&fnName=getContactList&json=1');
    $(element, el).autocomplete(contactUrl, {
      width: 200,
      selectFirst: false,
      minChars: 1,
      matchContains: true,
      delay: 400
    }).result(function(event, data) {
      var selectedCID = data[1];
      $(targetElement).val(selectedCID);
    });
  });

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
            $(self.targetElementId).val(response.contactID);
            $(self.elementId).val(response.displayName);
          }
          else {
            self.displayNewContactProfile(response);
          }
        }
      }).validate(CRM.validate.params);
    }

  });


  BookingInfo.Organisation = Backbone.Marionette.ItemView.extend({
    template: '#booking-info-organisation-template',
    onRender: function(){
      BookingApp.vent.trigger('init:autocomplete', '#organisation', '#organisation_select_id', this.$el);
    },
    events: {
      'change .crm-booking-create-contact-select': 'createContactDialog',
    },
    createContactDialog: function(e) {
     // var thisView = this;
      var profile = $(e.target).val();
      if(profile.length) {
        BookingApp.vent.trigger('render:dialog',
          profile,
          $(e.target).find(':selected').text(),
          '#organisation',
          '#organisation_select_id');
        $(e.target).val('');
      }
    },

  });

  BookingInfo.Contact = Backbone.Marionette.ItemView.extend({
    template: '#booking-info-contact-template',

    onRender: function(){
      BookingApp.vent.trigger('init:autocomplete', '#contact', '#contact_select_id', this.$el);
    },
    events: {
      'change .crm-booking-create-contact-select': 'createContactDialog',
    },
    createContactDialog: function(e) {
     // var thisView = this;
      var profile = $(e.target).val();
      if(profile.length) {
        BookingApp.vent.trigger('render:dialog',
          profile,
          $(e.target).find(':selected').text(),
          '#contact',
          '#contact_select_id');
        $(e.target).val('');
      }
    },
  });
});
