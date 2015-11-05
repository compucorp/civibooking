CRM.BookingApp.module("Utils", function(Utils, BookingApp, Backbone, Marionette, $, _){

  //http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
  Utils.getCurrentUnixTimstamp = function () {
    return unix = Math.round(+new Date()/1000);
  };

  //http://stackoverflow.com/questions/10834796/validate-that-a-string-is-a-positive-integer
  Utils.isPositiveInteger = function (n){
   return 0 === n % (!isNaN(parseFloat(n)) && 0 <= ~~n);
  };
  
  Utils.isPositiveNumber = function (n){
   return !_.isNumber(n) && 0 <= n;
  };

});



