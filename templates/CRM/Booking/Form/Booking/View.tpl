{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{* View existing booking  record. *}
<div class="crm-block crm-content-block crm-booking-view-form-block">
    <h3>{ts}View Booking{/ts}</h3>
    <div class="action-link">
        <div class="crm-submit-buttons">
          {if call_user_func(array('CRM_Core_Permission','check'), 'edit booking')}
            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking&key=$searchKey"}
          {/if}
               <a class="button" href="{crmURL p='civicrm/booking/add/' q=$urlParams}" accesskey="e"><span><div class="icon edit-icon"></div> {ts}Edit{/ts}</span></a>
            {/if}
            {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviBooking')}
                {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=event"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=booking&key=$searchKey"}
          {/if}
                <a class="button" href="{crmURL p='civicrm/contact/view/booking' q=$urlParams}"><span><div class="icon delete-icon"></div> {ts}Delete{/ts}</span></a>
            {/if}
            {include file="CRM/common/formButtons.tpl" location="top"}
        </div>
    </div>
    <table class="crm-info-panel">
      <tr class="crm-bookingview-form-block-displayName">
        <td class="label">{ts}Primary Contact{/ts}</td>
        <td class="bold">
          <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contact_id"}" title="view contact record">{$displayName}</a>
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking">
        <td class="label">{ts}Booking{/ts}</td><td>
          {$title}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-associated-contact">
        <td class="label">{ts}Associated Contact{/ts}</td><td>
          {$secondaryContactDisplayName}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-date-made">
        <td class="label">{ts}Date Made{/ts}</td><td>
          {$created_date}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-event-date">
        <td class="label">{ts}Event Date{/ts}</td><td>
          {$event_date}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-po-no">
        <td class="label">{ts}PO NO{/ts}</td><td>
          {$po_number}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking_status">
        <td class="label">{ts}Booking status{/ts}</td><td>
          {$status}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking_payment_status">
        <td class="label">{ts}Payment status{/ts}</td><td>
          {if $payment_status eq ''}
           {ts}Unpaid{/ts}
          {else}
           {$payment_status}
          {/if}
        </td>
      </tr>
     <tr class="crm-bookingview-form-block-description">
        <td class="label">{ts}Description{/ts}</td><td>
          {$description}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-note">
        <td class="label">{ts}Note{/ts}</td><td>
          {$note}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-participant-estimate">
        <td class="label">{ts}Participant estimated{/ts}</td><td>
          {$participants_estimate}
        </td>
      </tr>
       <tr class="crm-bookingview-form-block-paritipant-actual">
        <td class="label">{ts}Participant actual{/ts}</td><td>
          {$participants_actual}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Sub Total{/ts}</td><td>
          {$sub_total}
        </td>
      </tr>
         <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Discount amount{/ts}</td><td>
          {$discount_amount}
        </td>
      </tr>
         <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Total{/ts}</td><td>
          {$total_amount}
        </td>
      </tr>
    </table>
    <h3>{ts}Resources{/ts}</h3>
    <table class="selector">
      <thead class="sticky">
        <tr>
          <th scope="col">{ts}Resource{/ts}</th>
          <th scope="col">{ts}Start Date{/ts}</th>
          <th scope="col">{ts}End Date{/ts}</th>
          <th scope="col">{ts}Configuration{/ts}</th>
          <th scope="col">{ts}Note{/ts}</th>
          <th scope="col">{ts}Price Per Unit{/ts}</th>
          <th scope="col">{ts}Quantity{/ts}</th>
          <th scope="col">{ts}Total Amount{/ts}</th>
        </tr>
      </thead>
      {foreach from=$slots item=slot}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$slot.resource_label}</td>
        <td>{$slot.start}</td>
        <td>{$slot.end}</td>
        <td>{$slot.config_label}</td>
        <td>{$slot.note}</td>
        <td>{$slot.unit_price}</td>
        <td>{$slot.quantity}</td>
        <td>{$slot.total_amount}</td>
      </tr>
      {/foreach}
    </table>
    {if $sub_slots}
     <h3>{ts}Unlmited Resources{/ts}</h3>
     <table class="selector">
      <thead class="sticky">
        <tr>
          <th scope="col">{ts}Resource{/ts}</th>
          <th scope="col">{ts}Parent Resoruce{/ts}</th>
          <th scope="col">{ts}Time Required{/ts}</th>
          <th scope="col">{ts}Configuration{/ts}</th>
          <th scope="col">{ts}Note{/ts}</th>
          <th scope="col">{ts}Price Per Unit{/ts}</th>
          <th scope="col">{ts}Quantity{/ts}</th>
          <th scope="col">{ts}Total Amount{/ts}</th>
        </tr>
      </thead>

      {foreach from=$sub_slots item=subSlot}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$subSlot.resource_label}</td>
        <td>{$subSlot.parent_resource_label}</td>
        <td>{$subSlot.time_required}</td>
        <td>{$subSlot.config_label}</td>
        <td>{$subSlot.note}</td>
        <td>{$subSlot.unit_price}</td>
        <td>{$subSlot.quantity}</td>
        <td>{$subSlot.total_amount}</td>
      </tr>
      {/foreach}
    </table>
    {/if}
    {if $adhoc_charges}
     <h3>{ts}Adhoc Charges Items{/ts}</h3>
     <table class="selector">
      <thead class="sticky">
        <tr>
          <th scope="col">{ts}Item{/ts}</th>
          <th scope="col">{ts}Price Per Unit{/ts}</th>
          <th scope="col">{ts}Quantity{/ts}</th>
          <th scope="col">{ts}Total Amount{/ts}</th>
        </tr>
      </thead>
      {foreach from=$adhoc_charges item=charges}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$charges.item_label}</td>
        <td>{$charges.unit_price}</td>
        <td>{$charges.quantity}</td>
        <td>{$charges.total_amount}</td>
      </tr>
      {/foreach}
    </table>
    {/if}
    <div class="crm-submit-buttons">
          {if call_user_func(array('CRM_Core_Permission','check'), 'edit booking')}
            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=booking&key=$searchKey"}
          {/if}
               <a class="button" href="{crmURL p='civicrm/booking/add/' q=$urlParams}" accesskey="e"><span><div class="icon edit-icon"></div> {ts}Edit{/ts}</span></a>
            {/if}
            {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviBooking')}
                {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=booking"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=booking&key=$searchKey"}
          {/if}
                <a class="button" href="{crmURL p='civicrm/contact/view/booking' q=$urlParams}"><span><div class="icon delete-icon"></div> {ts}Delete{/ts}</span></a>
            {/if}
            {include file="CRM/common/formButtons.tpl" location="top"}
      </div>
</div>
