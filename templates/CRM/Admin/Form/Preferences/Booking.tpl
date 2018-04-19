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

<h3>{ts}Booking System Configuration{/ts}</h3>
<div class="crm-block crm-form-block crm-booking-system-config-form-block">

  {* CVB-101: Remove out for now
  <fieldset><legend>{ts}Time{/ts}</legend>
  <table class="form-layout-compressed">
    <tr class="crm-booking-system-config-form-block-day_start_at">
      <td class="label">{$form.day_start_at.label}</td>
      <td>
         {$form.day_start_at.html}
      </td>
    </tr>
    <tr class="crm-booking-system-config-form-block-day_end_at">
      <td class="label">{$form.day_end_at.label}</td>
      <td>
         {$form.day_end_at.html}
      </td>
    </tr>
  *}
    {*
    <tr class="crm-booking-system-config-form-block-time_period">
      <td class="label">{$form.time_period.label}</td>
      <td>
         {$form.time_period.html}
      </td>
    </tr>
    *}
  {*
  </table>
  </fieldset>
  *}

  <fieldset><legend>{ts}Booking confirmation emails{/ts}</legend>
  <table class="form-layout-compressed">

    <tr class="crm-booking-system-config-form-block-cc_email_address">
        <td class="label">{$form.cc_email_address.label}</td><td>{$form.cc_email_address.html}</td>
    </tr>
    <tr class="crm-booking-system-config-form-block-bcc_email_address">
      <td class="label">{$form.bcc_email_address.label}</td>  <td>{$form.bcc_email_address.html} </td>
    </tr>
    <tr class="crm-booking-system-config-form-block-log_confirmation_email">
      <td class="label">{$form.log_confirmation_email.label}</td>  <td>{$form.log_confirmation_email.html} </td>
    </tr>
  </table>
  </fieldset>

  <fieldset><legend>{ts}Booking configuration setting{/ts}</legend>
  <table class="form-layout-compressed">
    <tr class="crm-booking-system-config-form-block-unlimited_resource_time_config">
      <td class="label">{$form.unlimited_resource_time_config.label}</td>  <td>{$form.unlimited_resource_time_config.html}
      <br/>
      <p class="description">{ts}Only allow unlimited resources to be booked within time span of the parent limited resource booking?{/ts}</p>
      </td>
    </tr>
  </table>
  </fieldset>


  <fieldset><legend>{ts}Slot colour scheme{/ts}</legend>
  <table class="form-layout-compressed">
    <tr class="crm-booking-system-config-form-block-slot_unavaliable_colour">
        <td class="label">{$form.slot_new_colour.label}</td><td>{$form.slot_new_colour.html}</td>
    </tr>
    <tr class="crm-booking-system-config-form-block-slot_unavaliable_colour">
        <td class="label">{$form.slot_booked_colour.label}</td><td>{$form.slot_booked_colour.html}</td>
    </tr>
    <tr class="crm-booking-system-config-form-block-slot_avaliable_colour">
        <td class="label">{$form.slot_provisional_colour.label}</td><td>{$form.slot_provisional_colour.html}</td>
    </tr>
    <tr class="crm-booking-system-config-form-block-slot_editing_colour">
        <td class="label">{$form.slot_being_edited_colour.label}</td><td>{$form.slot_being_edited_colour.html}</td>
    </tr>

  </table>
  </fieldset>
 <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

{crmScript ext=uk.co.compucorp.civicrm.booking file=templates/CRM/Admin/Form/Preferences/Booking.js}

{/crmScope}
