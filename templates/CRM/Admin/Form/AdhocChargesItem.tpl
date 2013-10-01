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
<h3>{if $action eq 1}{ts}New Additional Charges Item{/ts}{else}{ts}Edit Addiitonal Charges Item{/ts}{/if}</h3>
<div class="crm-block crm-form-block crm-bookingAdhocChargesItem-form-block">
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
  <table class="form-layout-compressed">
    <tr class="crm-bookingAdhocChargesItem-form-block-name">
        <td class="label">{$form.name.label}</td><td>{$form.name.html}</td>
    </tr>
    <tr class="crm-bookingAdhocChargesItem-form-block-label">
        <td class="label">{$form.label.label}</td><td>{$form.label.html}</td>
    </tr>
    <tr class="crm-bookingAdhocChargesItem-form-block-description">
        <td class="label">{$form.price.label}</td><td>{$form.price.html}</td>
    </tr>
     <tr class="crm-bookingAdhocChargesItem-form-block-weight">
        <td class="label">{$form.weight.label}</td><td>{$form.weight.html}</td>
    </tr>
    <tr class="crm-bookingAdhocChargesItem-form-block-is_active">
        <td class="label">{$form.is_active.label}</td><td>{$form.is_active.html}</td>
    </tr>
  </table>
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
