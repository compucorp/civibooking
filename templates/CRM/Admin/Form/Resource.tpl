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
<h3>{if $action eq 1}{ts}New Resource{/ts}{elseif $action eq 2}{ts}Edit Resource{/ts}{else}{ts}Delete Resource{/ts}{/if}</h3>
<div class="crm-block crm-form-block crm-bookingResource-form-block">
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
{if $action eq 8}
  <div class="messages status no-popup">
      <div class="icon inform-icon"></div>
        {ts}WARNING: Deleting this Resource  will result in the loss of all records which use the Resource. This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
  </div>
{else}
  <table class="form-layout-compressed">
    <tr class="crm-bookingResource-form-block-type">
      <td class="label">{$form.type_id.label}</td>
      <td>
         {$form.type_id.html}
      </td>
    </tr>
    <tr class="crm-bookingResource-form-block-label">
        <td class="label">{$form.label.label}</td><td>{$form.label.html}</td>
    </tr>
    <tr class="crm-bookingResource-form-block-description">
        <td class="label">{$form.description.label}</td><td>{$form.description.html}</td>
    </tr>
    <tr class="crm-bookingResource-form-block-location">
      <td class="label">{$form.location_id.label}</td>
      <td>
         {$form.location_id.html}
      </td>
    </tr>
    <tr class="crm-bookingResource-form-block-set_id">
      <td class="label">{$form.set_id.label}</td>
      <td>
         {$form.set_id.html}
         <br/>
        {capture assign=ftUrl}{crmURL p='civicrm/admin/resource/config_set' q="reset=1&action=add"}{/capture}
        {ts 1=$ftUrl}<a href='%1'>Click here</a> if you want to add new configuration set to your site.{/ts}
      </td>
    </tr>
     <tr class="crm-bookingResource-form-block-weight">
        <td class="label">{$form.weight.label}</td><td>{$form.weight.html}</td>
    </tr>
    <tr class="crm-bookingResource-form-block-is_unlimited">
        <td class="label">{$form.is_unlimited.label}</td><td>{$form.is_unlimited.html}</td>
    </tr>
    <tr class="crm-bookingResource-form-block-is_active">
        <td class="label">{$form.is_active.label}</td><td>{$form.is_active.html}</td>
    </tr>
  </table>
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
{/if}
</div>
{/crmScope}
