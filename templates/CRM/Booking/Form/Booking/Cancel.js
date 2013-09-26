cj(function($) {

  $('#cancellations').change(function(e) {
    var canPercentage = $(this).val();
    if(!isNaN(canPercentage)){
      var totalAmount = $('input[name="booking_total"]').val();
      var cancellationCharge = (totalAmount * canPercentage) / 100;
      $('input[name="cancellation_charge"]').val(cancellationCharge);
      $('#charge_amount').val(cancellationCharge);
    }
  });

  $(document).on('keypress keyup keydown', '#adjustment',  function(e) {
    var cancellationCharge = $('input[name="cancellation_charge"]').val();
    var additionalCharges = $(this).val();
    var amountToPay = parseFloat(cancellationCharge) + parseFloat(additionalCharges);
    if(isNaN(amountToPay)){
      amountToPay = cancellationCharge;
    }
    $('#charge_amount').val(amountToPay);
  });

});

