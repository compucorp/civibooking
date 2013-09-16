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
<tr>
  <td>
    <label>{$form.id.label}</label> <br />
    {$form.id.html}
  </td>
  <td>
    {$form.title.label}<br />
    {$form.title.html}
   </td>
</tr>

<tr>
  <td>
  <label>{ts}Booking Event Dates{/ts}</label> <br/>
  </td>
</tr>
<tr>
<tr>
  {include file="CRM/Core/DateRange.tpl" fieldName="event_start_date" from='_low' to='_high'}
</tr>

<tr>
  <td>
    <label>{$form.resource_id.label}</label> <br />
    {$form.resource_id.html}
  </td>
  <td>
    {$form.po_no.label}<br />
    {$form.po_no.html}
   </td>
</tr>

<tr>
  <td class="crm-booking-form-block-booking_status_id"><label>{ts}Booking Status{/ts}</label>
    <br />
    <div class="listing-box" style="width: auto; height: 120px">
    {foreach from=$form.booking_status_id item="booking_status_id_val"}
      <div class="{cycle values="odd-row,even-row"}">
        {$booking_status_id_val.html}
      </div>
    {/foreach}
    </div>
  </td>
  <td class="crm-booking-form-block-payment_status_id"><label>{ts}Payment Status{/ts}</label>
    <br />
    <div class="listing-box" style="width: auto; height: 120px">
    {foreach from=$form.payment_status_id item="payment_status_id_val"}
      <div class="{cycle values="odd-row,even-row"}">
        {$payment_status_id_val.html}
      </div>
    {/foreach}
    </div><br />
  </td>
</tr>







