//http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
CRM.BookingApp.module("Utils", function(Utils, BookingApp, Backbone, Marionette, $, _){

  Utils.getCurrentUnixTimstamp = function () {
    return unix = Math.round(+new Date()/1000);
  }

});



