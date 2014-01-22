

cj(function($) {

  function calculateCancellationCharge() {
    var canPercentage = $('#cancellations').val();
    if(!isNaN(canPercentage)){
      var totalAmount = $('input[name="booking_total"]').val();
      var cancellationCharge = (totalAmount * canPercentage) / 100;
      $('input[name="cancellation_charge"]').val(cancellationCharge);

      $('#cancellation_charge_display').text(cancellationCharge.toFixed(2));
      $('#charge_amount').text(cancellationCharge.toFixed(2));
      $('#total_amount').val(cancellationCharge.toFixed(2));

    }
  }

  function calculateAdditionalCharge(){
  	var cancellationCharge = $('input[name="cancellation_charge"]').val();
    var additionalCharges = $('#adjustment').val();
    var amountToPay = parseFloat(cancellationCharge) + parseFloat(additionalCharges);
    if(isNaN(amountToPay)){
      amountToPay = cancellationCharge;
    }
    $('#charge_amount').text(amountToPay);
  }

  $(document).ready(function(){
  	calculateCancellationCharge();
  	calculateAdditionalCharge();
  });
	
  $('#cancellations').change(function(){
  	calculateCancellationCharge();
  });

  $(document).on('keypress keyup keydown', '#adjustment',  function() {
    calculateAdditionalCharge();
  });

});

