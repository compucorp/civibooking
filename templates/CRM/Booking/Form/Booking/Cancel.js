

cj(function($) {

  function calculateCharge(){
  	var canPercentage = $('#cancellations').val();
    if(!isNaN(canPercentage)){
      var totalAmount = $('input[name="booking_total"]').val();
      var cancellationCharge = (totalAmount * canPercentage) / 100;
      $('input[name="cancellation_charge"]').val(cancellationCharge);

      $('#cancellation_charge_display').text(cancellationCharge.toFixed(2));
    }
  	var cancellationCharge = $('input[name="cancellation_charge"]').val();
    var additionalCharges = $('#adjustment').val();
    var amountToPay = parseFloat(cancellationCharge) + parseFloat(additionalCharges);
    if(isNaN(amountToPay)){
      amountToPay = parseFloat(cancellationCharge);
    }
    $('#charge_amount').text(amountToPay.toFixed(2));
    $('#total_amount').val(amountToPay.toFixed(2));
  }

  $(document).ready(function(){
  	calculateCharge();
  });
	
  $('#cancellations').change(function(){
  	calculateCharge();
  });
  
  $(document).on('keypress keyup keydown', '#adjustment',  function() {
    calculateCharge();
  });

});

