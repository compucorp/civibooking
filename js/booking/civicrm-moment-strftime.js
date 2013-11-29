/**
 * Referrences
 * moment-strftime
 * https://github.com/benjaminoakes/moment-strftime
 * PHP - strftime
 * http://php.net/manual/en/function.strftime.php
 * Moment - format
 * http://momentjs.com/docs/#/displaying/format/
 * 
 */

var replacements = {
    'E%f': 'Do', //modified to match CiviCRM date format
        
    a: 'ddd',
    A: 'dddd',
    b: 'MMM',
    B: 'MMMM',
    d: 'DD',
    E: 'D',
    f: '',
    H: 'HH',
    I: 'hh',
    j: 'DDDD',
    k: 'H',
    l: 'h', 
    m: 'MM',
    M: 'mm',
    P: 'A', 
    p: 'A',
    r: 'HH:mm:ss',
    R: 'HH:mm',
    S: 'ss',
    T: 'HH:mm:ss',
    Z: 'z',
    w: 'd',
    y: 'YY',
    Y: 'YYYY',
    X: 'LT',
    z: '',
    Z: '',
    '%': '%'
  };

/**
 * Additional strftime function to moment.js
 */
(function() {
  var moment;

  if (typeof require !== "undefined" && require !== null) {
    moment = require('moment');
  } else {
    moment = this.moment;
  }
  
  moment.fn.strftime = function(format) {
    var key, momentFormat, value;
    momentFormat = format;
    for (key in replacements) {
      value = replacements[key];
      momentFormat = momentFormat.replace("%" + key, value);
    }
    return this.format(momentFormat);
  };

  if (typeof module !== "undefined" && module !== null) {
    module.exports = moment;
  } else {
    this.moment = moment;
  }

}).call(this);

/**
 * Convert Civicrm Date format to moment.js date format
 */
function toMomentDateFormat(str){
  var value;
  for (key in replacements) {
    value = replacements[key];
    str = str.replace("%" + key, value);
  }
  return str;
}


