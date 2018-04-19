{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
<h3>{ts}Cancel Booking?{/ts}</h3>
<div class="crm-form-block crm-cancel-booking-form-block">
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
  <div class="crm-section">
    <div class="label">{ts}Event Start Date{/ts}</div>
    <div class="content">{$booking.booking_start_date|crmDate}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Event End Date{/ts}</div>
    <div class="content">{$booking.booking_end_date|crmDate}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.cancellation_date.label}</div>
    <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=cancellation_date}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Resource Fees{/ts}</div>
    <div class="content">{$booking.resource_fee|number_format:2:".":","}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Unlimited Resource Fee{/ts}</div>
    <div class="content">{$booking.sub_resource_fee|number_format:2:".":","}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Additional Charges{/ts}</div>
    <div class="content">{$booking.adhoc_charges|number_format:2:".":","}</div>
  </div>
   <div class="crm-section">
    <div class="label">{ts}Discount Amount{/ts}</div>
    <div class="content">{$booking.discount_amount|number_format:2:".":","}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Booking Amount{/ts}</div>
    <div class="content">{$booking.booking_total|number_format:2:".":","}</div>
  </div>
   <div class="crm-section">
    <div class="label">{$form.cancellations.label}</div>
    <div class="content">{$form.cancellations.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Cancellation Charge{/ts}</div>
    <div class="content"><span id="cancellation_charge_display">{ts}0{/ts}</span>{$form.cancellation_charge.html}</div>
  </div>
   <div class="crm-section">
    <div class="label">{$form.adjustment.label}</div>
    <div class="content">{$form.adjustment.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.comment.label}</div>
    <div class="content">{$form.comment.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Amount to Pay{/ts}</div>
    <div class="content"><span id="charge_amount">{ts}0{/ts}</span></div>
  </div>
  {include file="CRM/Booking/Form/Booking/Common.tpl"}
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

{* Set today date for datepicker *}
{literal}
  <script type="text/javascript">
  cj(function($) {
    $('#cancellation_date_display').datepicker("setDate", (new Date()));
  });
  </script>
{/literal}
{/crmScope}
