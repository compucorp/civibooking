CRM.BookingApp.module("Utils", function(Utils, BookingApp, Backbone, Marionette, $, _){

  //http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
  Utils.getCurrentUnixTimstamp = function () {
    return unix = Math.round(+new Date()/1000);
  };

  //http://stackoverflow.com/questions/10834796/validate-that-a-string-is-a-positive-integer
  Utils.isPositiveInteger = function (n){
   return 0 === n % (!isNaN(parseFloat(n)) && 0 <= ~~n);
  };
  
  //http://stackoverflow.com/questions/9716468/is-there-any-function-like-isnumeric-in-javascript-to-validate-numbers
  Utils.isNumeric = function (n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  };

});



