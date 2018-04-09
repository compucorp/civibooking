{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
<h3>{if $action eq 2}{ts}Update Status/Record Contribution{/ts}{else}{ts}Delete Booking{/ts}{/if}</h3>
<div class="crm-form-block crm-update-booking-form-block">
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
{if $action eq 8}
  <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
        {ts}WARNING: Deleting this Booking  will result in the loss of all records which use the Booking. This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
  </div>
{else}
  <div class="crm-section">
    <div class="label">{ts}Booking ID{/ts}</div>
    <div class="content">{$booking.id}</div>
  </div>
  <div class="crm-section">
    <div class="label">{ts}Current status{/ts}</div>
    <div class="content">{$booking.status}</div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.booking_status.label}</div>
    <div class="content">{$form.booking_status.html}</div>
  </div>
  {include file="CRM/Booking/Form/Booking/Common.tpl"}
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
{/if}
</div>
{/crmScope}
