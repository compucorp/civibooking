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
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
<div id="main-container" class="crm-form-block">
  <div id="resource-main" >

  </div>
  <div id="crm-booking-dialog" class="crm-container"></div>
  <div class="clear"></div>
  <div class="hiddenElement">
  {$form.sub_total.label} {$form.sub_total.html}
  {$form.adhoc_charge.label} {$form.adhoc_charge.html}
  {$form.total_price.label} {$form.total_price.html}
  {$form.discount_amount.label} {$form.discount_amount.html}
  {$form.sub_resources.html}
  </div>
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">

var crmDateFormat = "{/literal}{$dateFormat}{literal}";   //retrieve crmDateFormat

cj(function() {
  CRM.BookingApp.start();
});
</script>
{/literal}
