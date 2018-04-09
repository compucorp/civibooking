{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
<div class="crm-section">
  <div class="label">{$form.send_confirmation.label}</div>
  <div class="content">{$form.send_confirmation.html}</div>
</div>
 <fieldset id="email-confirmation" class="hiddenElement"><legend>{ts}Email booking conformation{/ts}</legend>
  <div class="crm-section">
    <div class="label">{$form.from_email_address.label}</div>
    <div class="content">{$form.from_email_address.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.email_to.label}</div>
    <div class="content">{$form.email_to.html}</div>
   </div>
   
{*
  <div class="crm-section">
    <div class="label">{ts}Receipt Message{/ts}</div>
    <div class="content">
    	<span class="description">{ts}If you need to include a special message for this booking contacts, enter it here. Otherwise, the confirmation email will include the standard receipt message configured under System Message Templates.{/ts}</span>
    </div>
    <div class="label">{$form.receipt_header_message.label}</div>
    <div class="content">
      {$form.receipt_header_message.html}
    </div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.receipt_footer_message.label}</div>
    <div class="content">
      {$form.receipt_footer_message.html}
    </div>
  </div>
*}
  
</fieldset>
<div class="crm-section">
    <div class="label">{ts}Payment status{/ts}</div>
    <div class="content">
      {if $booking.payment_status eq ''}
        {ts}Unpaid{/ts}
      {else}
        {$booking.payment_status}
      {/if}
    </div>
</div>
{if $rows.0.contribution_id}
<!-- Show Contribution record  -->
    <fieldset>
    {include file="CRM/Contribute/Form/Selector.tpl" context="Search"}
    </fieldset>
{else}
<!-- Show Record Payment form -->
<div class="crm-section">
  <div class="label">{$form.record_contribution.label}</div>
  <div class="content">{$form.record_contribution.html}</div>
</div>

<fieldset id="payment-detail" class="hiddenElement"><legend>{ts}Payment details{/ts}</legend>
  <div class="crm-section">
    <div class="label">{$form.select_payment_contact.label}</div>
    <div class="content">{$form.select_payment_contact.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.total_amount.label}</div>
    <div class="content">
      {$form.currencySymbol.html|crmAddClass:two}&nbsp;{$form.total_amount.html|crmAddClass:eight}<br/>
      <span class="description"> {ts}Booking payment amount. A contribution record will be created for this amount.{/ts} </span>
    </div>
  </div>
  <div class="crm-section">
   <div class="label"> {$form.financial_type_id.label}</div>
   <div class="content">{$form.financial_type_id.html}<br/>
     <span class="description">{ts}Select the appropriate financial type for this payment.{/ts}</span>
   </div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.receive_date.label}</div>
    <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=receive_date}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.payment_instrument_id.label}</div>
    <div class="content">{$form.payment_instrument_id.html}</div>
  </div>
  <div id="checkNumber" class="crm-section">
    <div class="label">{$form.check_number.label}</div>
    <div class="content">{$form.check_number.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.trxn_id.label}</div>
    <div class="content">{$form.trxn_id.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.contribution_status_id.label}</div>
    <div class="content">{$form.contribution_status_id.html}</div>
  </div>
  {* Hide include_payment_information option for now.
  <div class="crm-section">
    <div class="label"></div>
    <div class="content">{$form.include_payment_information.html}</div>
  </div>
   *}
</fieldset>
{/if}

{literal}
<script type="text/javascript">
cj(function($) {

  $('#send_confirmation').change(function() {
    if($(this).is(":checked")){
     $('#email-confirmation').show();
    }else{
     $('#email-confirmation').hide();
    }
  });


  $('#record_contribution').change(function() {
    if($(this).is(":checked")){
     $('#payment-detail').show();
    }else{
     $('#payment-detail').hide();
    }
  });

  function loadHiddenElements(){
    var input = $( "input[name='record_contribution']" );
    if(input.is(":checked")){
      $('#payment-detail').show();
    }
    var input = $( "input[name='send_confirmation']" );
    if(input.is(":checked")){
      $('#email-confirmation').show();
    }
  }

  $(document).ready(loadHiddenElements);

});
</script>
{/literal}
{include file="CRM/common/showHideByFieldValue.tpl"
trigger_field_id    ="payment_instrument_id"
trigger_value       = '4'
target_element_id   ="checkNumber"
target_element_type ="block"
field_type          ="select"
invert              = 0
}

{/crmScope}
