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
{* View existing event registration record. *}
<div class="crm-block crm-content-block crm-booking-view-form-block">
    <h3>{ts}View Booking{/ts}</h3>
    <div class="action-link">
        <div class="crm-submit-buttons">
          {if call_user_func(array('CRM_Core_Permission','check'), 'edit booking')}
            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=event"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=event&key=$searchKey"}
          {/if}
               <a class="button" href="{crmURL p='civicrm/booking/add/' q=$urlParams}" accesskey="e"><span><div class="icon edit-icon"></div> {ts}Edit{/ts}</span></a>
            {/if}
            {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviBooking')}
                {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=event"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=event&key=$searchKey"}
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
          {$secondary_contact_id}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-date-made">
        <td class="label">{ts}Date Made{/ts}</td><td>
          {$created_date}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-event-date">
        <td class="label">{ts}Event Date{/ts}</td><td>

        </td>
      </tr>
      <tr class="crm-bookingview-form-block-po-no">
        <td class="label">{ts}PO NO{/ts}</td><td>
          {$po_number}
        </td>
      </tr>
       <tr class="crm-bookingview-form-block-price">
        <td class="label">{ts}Price{/ts}</td><td>
          {$price}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking_status">
        <td class="label">{ts}Booking status{/ts}</td><td>
          {$status}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-booking_payment_status">
        <td class="label">{ts}Payment status{/ts}</td><td>
          {$payment_status}
        </td>
      </tr>
     <tr class="crm-bookingview-form-block-description">
        <td class="label">{ts}Description{/ts}</td><td>
          {$description}
        </td>
      </tr>
      <tr class="crm-bookingview-form-block-discount_amount">
        <td class="label">{ts}Discount amount{/ts}</td><td>
          {$discount_amount}
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
    </table>
    <table class="selector">
      <thead class="sticky">
        <tr>
          <th scope="col">{ts}Resource{/ts}</th>
          <th scope="col">{ts}Configuration{/ts}</th>
          <th scope="col">{ts}Start date{/ts}</th>
          <th scope="col">{ts}End date{/ts}</th>
          <th scope="col">{ts}Note{/ts}</th>
        </tr>
      </thead>

      {foreach from=$slots item=slot}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$slot.resource_id}</td>
        <td>{$slot.config_id}</td>
        <td>{$slot.start}</td>
        <td>{$slot.end}</td>
        <td>{$slot.note}</td>
      </tr>
      {if $slot.sub_slots}
      <tr>
        <td colspan="1">{ts}<h3>Sub slots</h3>{/ts}</td>
        <td colspan="4">
        <table >
            <thead class="sticky">
              <tr>
                <th scope="col">{ts}Resource{/ts}</th>
                <th scope="col">{ts}Configuration{/ts}</th>
                <th scope="col">{ts}Time required{/ts}</th>
                <th scope="col">{ts}Note{/ts}</th>
              </tr>
            </thead>
            {foreach from=$slot.sub_slots item=subSlot}
            <tr class="{cycle values="odd-row,even-row"}">
              <td>{$subSlot.resource_id}</td>
              <td>{$subSlot.config_id}</td>
              <td>{$slot.time_required}</td>
              <td>{$slot.note}</td>
            </tr>
            {/foreach}
        </table>

        <tr>
        {/if}
      {/foreach}
    </table>
    <div class="crm-submit-buttons">
          {if call_user_func(array('CRM_Core_Permission','check'), 'edit booking')}
            {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=event"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&selectedChild=event&key=$searchKey"}
          {/if}
               <a class="button" href="{crmURL p='civicrm/booking/add/' q=$urlParams}" accesskey="e"><span><div class="icon edit-icon"></div> {ts}Edit{/ts}</span></a>
            {/if}
            {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviBooking')}
                {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=event"}
          {if ($context eq 'search' ) && $searchKey}
          {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&selectedChild=event&key=$searchKey"}
          {/if}
                <a class="button" href="{crmURL p='civicrm/contact/view/booking' q=$urlParams}"><span><div class="icon delete-icon"></div> {ts}Delete{/ts}</span></a>
            {/if}
            {include file="CRM/common/formButtons.tpl" location="top"}
      </div>
</div>
