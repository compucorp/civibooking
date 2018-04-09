{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
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

<table class="form-layout">

<tr>
  <td>
    <label>{$form.booking_id.label}</label> <br />
    {$form.booking_id.html}
  </td>
  <td>
    {$form.booking_title.label}<br />
    {$form.booking_title.html}
   </td>
</tr>

<tr>
  <td>
  <label>{ts}Date Booking Made{/ts}</label> <br/>
  </td>
</tr>
<tr>
  {include file="CRM/Core/DateRange.tpl" fieldName="booking_event_date" from='_low' to='_high'}
</tr>
<tr>
  <td>
  <label>{ts}Start Dates{/ts}</label> <br/>
  </td>
</tr>
<tr>
  {include file="CRM/Core/DateRange.tpl" fieldName="booking_start_date" from='_low' to='_high'}
</tr>
<tr>
  <td>
  <label>{ts}End Dates{/ts}</label> <br/>
  </td>
</tr>
<tr>
  {include file="CRM/Core/DateRange.tpl" fieldName="booking_end_date" from='_low' to='_high'}
</tr>

<tr>
{* CVB-116:Remove ResourceType as of pending searching issue.
  <td>
    <label>{$form.booking_resource_id.label}</label> <br />
    {$form.booking_resource_id.html}
  </td>
*}
  <td>
    {$form.booking_po_no.label}<br />
    {$form.booking_po_no.html}
   </td>
  <td>
  </td>
</tr>

<tr>
  <td class="crm-booking-form-block-booking_status_id"><label>{ts}Status{/ts}</label>
    <br />
    <div class="listing-box" style="width: auto; height: 120px">
    {foreach from=$form.booking_status_id item="booking_status_id_val"}
      <div class="{cycle values="odd-row,even-row"}">
        {$booking_status_id_val.html}
      </div>
    {/foreach}
    </div>
  </td>
  
  {*    remove Payment Status as of pending searching issue.
  <td class="crm-booking-form-block-payment_status_id"><label>{ts}Payment Status{/ts}</label>
    <br />
    <div class="listing-box" style="width: auto; height: 120px">
    {foreach from=$form.booking_payment_status_id item="payment_status_id_val"}
      <div class="{cycle values="odd-row,even-row"}">
        {$payment_status_id_val.html}
      </div>
    {/foreach}
    </div><br />
  </td>
  *}
</tr>
</table>
{/crmScope}
