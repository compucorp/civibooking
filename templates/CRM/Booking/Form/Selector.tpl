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
{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}
{strip}
<table class="selector">
  <thead class="sticky">
  <tr>
    {if !$single and $context eq 'Search' }
        <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th>
    {/if}
    {foreach from=$columnHeaders item=header}
        <th scope="col">
        {if $header.sort}
          {assign var='key' value=$header.sort}
          {$sort->_response.$key.link}
        {else}
          {$header.name}
        {/if}
        </th>
    {/foreach}
  </tr>
  </thead>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id="rowid{$row.booking_id}" class="{cycle values="odd-row,even-row"} crm-booking_{$row.booking_id}">
    {if !$single }
        {if $context eq 'Search' }
          {assign var=cbName value=$row.checkbox}
          <td>{$form.$cbName.html}</td>
       {/if}
        <td class="crm-booking-contact_type">{* {$row.contact_type} *}</td>
        <td class="crm-booking-sort_name"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="{ts}View contact record{/ts}">{$row.sort_name}</a></td>
    {/if}

    <td class="crm-booking-title">
        {$row.booking_title}
    </td>

    <td class="crm-booking-associated-contact">
        <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.booking_associated_contact_id`"}" title="{ts}View contact record{/ts}">{$row.booking_associated_contact}
        </a>
    </td>
    <td class="crm-booking-event_date">
        {$row.booking_event_date|crmDate}
    </td>
	<td class="crm-booking-start_date">
        {$row.booking_start_date|crmDate}
    </td>
	<td class="crm-booking-end_date">
        {$row.booking_end_date|crmDate}
    </td>
    <td class="crm-booking-total_amount">
        {$row.booking_total_amount}
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

    <td>{$row.action|replace:'xx':$row.booking_id}</td>
  </tr>
  {/foreach}
</table>
{/strip}

{if $context EQ 'Search'}
 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";
    on_load_init_checkboxes(fname);
 </script>
{/if}

{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="bottom"}
{/if}
{/crmScope}
