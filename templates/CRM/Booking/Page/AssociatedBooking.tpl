{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
<table class="selector">
  <thead class="sticky">
    <tr>
      <th scope="col">{ts}Booking Contact{/ts}</th>
      <th scope="col">{ts}Title{/ts}</th>
      <th scope="col">{ts}Date Booking Made{/ts}</th>
      <th scope="col">{ts}Date Booking Start{/ts}</th>
      <th scope="col">{ts}Date Booking End{/ts}</th>
      <th scope="col">{ts}Price{/ts}</th>
      <th scope="col">{ts}Booking Status{/ts}</th>
      <th scope="col">{ts}Payment Status{/ts}</th>
      <th></th>
    </tr>
  </thead>
  {foreach from=$associatedBooking item=row}
  <tr id="rowid{$row.id}" class="{cycle values="odd-row,even-row"} crm-booking_{$row.id}">
    <td class="crm-booking-contact">
      <a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.primary_contact_id`"}" id="view_contact" title="{ts}View Booking Contact Record{/ts}">{$row.primary_contact_name}</a>
    </td>
    <td class="crm-booking-title">
        {$row.title}
    </td>
    <td class="crm-booking-event_date">
        {$row.booking_date|crmDate}
    </td>
    <td class="crm-booking-start_date">
        {$row.start_date|crmDate}
    </td>
    <td class="crm-booking-end_date">
        {$row.end_date|crmDate}
    </td>
    <td class="crm-booking-total_amount">
        {$row.total_amount}
    </td>
     <td class="crm-booking-status">
        {$row.booking_status}
    </td>
    <td class="crm-booking-payment-status">
      {if $row.booking_payment_status eq ''}
           {ts}Unpaid{/ts}
      {else}
        {$row.booking_payment_status}
      {/if}
    </td>
    <td><a href="{crmURL p="civicrm/contact/view/booking" q="reset=1&id=`$row.id`&cid=`$contactId`&action=view&context=booking&selectedChild=booking"}" title="{ts}View related booking{/ts}">{ts}View{/ts}</a></td>
  </tr>
  {/foreach}
</table>
{/crmScope}
