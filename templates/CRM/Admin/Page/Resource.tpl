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
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/Resource.tpl"}
{else}
{if $rows}
<div id="ltype">
        {strip}
        {* handle enable/disable actions*}
        {include file="CRM/common/enableDisableApi.tpl"}
        <table class="selector">
        <tr class="columnheader">
            <th >{ts}Label{/ts}</th>
            <th >{ts}Description{/ts}</th>
            <th >{ts}Type{/ts}</th>
            <th >{ts}Location{/ts}</th>
            <th >{ts}Weight{/ts}</th>
            <th >{ts}Unlimited?{/ts}</th>
            <th >{ts}Enabled?{/ts}</th>
            <th ></th>
        </tr>
        {foreach from=$rows item=row}
        <tr id="resource-{$row.id}" class="crm-booking_resource crm-entity {cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td class="crm-booking-resource-name">{$row.label}</td>
            <td class="crm-booking-resource-description">{$row.description}</td>
            <td class="crm-booking-resource-type">{$row.type}</td>
            <td class="crm-booking-resource-location">{$row.location}</td>
            <td class="crm-booking-resource-weight">{$row.weight}</td>
            <td class="crm-booking-resource-is_unlimited">{if $row.is_unlimited eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td id="row_{$row.id}_status" class="crm-booking-resource-is_active">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
        <div class="action-link">
          <a href="{crmURL q="action=add&reset=1"}" id="new" class="button"><span><div class="icon add-icon"></div>{ts}Add Resource{/ts}</span></a>
        </div>
        {/if}
</div>
{elseif $action ne 1}
    <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
        {ts}There are no resources.{/ts}
     </div>
     <div class="action-link">
       <a href="{crmURL p='civicrm/admin/resource' q="action=add&reset=1"}" id="newResource" class="button"><span><div class="icon add-icon"></div>{ts}Add Resource{/ts}</span></a>
     </div>
{/if}
{/if}
{/crmScope}
