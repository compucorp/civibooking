<h3>{ts}Cancel Booking?{/ts}</h3>
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
<div class="crm-form-block crm-cancel-booking-form-block">
  <div class="crm-section">
    <div class="label">{$form.event_date.label}</div>
    <div class="content">{$form.event_date.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.cancellation_date.label}</div>
    <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=cancellation_date}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.resource_fee.label}</div>
    <div class="content">{$form.resource_fee.html}</div>
  </div>
    <div class="crm-section">
    <div class="label">{$form.sub_resource_fee.label}</div>
    <div class="content">{$form.sub_resource_fee.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.adhoc_charges.label}</div>
    <div class="content">{$form.adhoc_charges.html}</div>
  </div>
   <div class="crm-section">
    <div class="label">{$form.discount_amount.label}</div>
    <div class="content">{$form.discount_amount.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.booking_total.label}</div>
    <div class="content">{$form.booking_total.html}</div>
  </div>
   <div class="crm-section">
    <div class="label">{$form.cancellations.label}</div>
    <div class="content">{$form.cancellations.html}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.cancellation_charge.label}</div>
    <div class="content">{$form.cancellation_charge.html}</div>
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
    <div class="label">{$form.charge_amount.label}</div>
    <div class="content">{$form.charge_amount.html}</div>
  </div>
  {include file="CRM/Booking/Form/Payment.tpl"}
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
