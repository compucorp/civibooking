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
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
<div id="main-container" >
  <div id="booking-detail-container" class="crm-form-block">
      {$form.resources.html}
      <div class="crm-section" id="contact-container">
          <div class="label">
            {$form.primary_contact_id.label}
  
          </div>
          <div class="content">
              {$form.primary_contact_id.html}
          </div>
      </div>
      <div class="crm-section"  id="organisation-container">
          <div class="label">
            {$form.secondary_contact_id.label}
  
          </div>
          <div class="content">
              {$form.secondary_contact_id.html}
          </div>
      </div>
      <div class="crm-section">
          <div class="label">
            {$form.booking_status.label}

          </div>
          <div class="content">
              {$form.booking_status.html}
          </div>
      </div>
      <div class="crm-section">
          <div class="label">
            {$form.po_no.label}

          </div>
          <div class="content">
              {$form.po_no.html}
          </div>
      </div>
      <fieldset><legend>{ts}Booking information{/ts}</legend>
        <div class="crm-section">
          <div class="label">
            {$form.title.label}

          </div>
          <div class="content">
              {$form.title.html}
          </div>
        </div>
         <div class="crm-section">
          <div class="label">
            {$form.event_start_date.label}

          </div>
          <div class="content">
            {include file="CRM/common/jcalendar.tpl" elementName=event_start_date}
          </div>
        </div>
         <div class="crm-section">
          <div class="label">
           {$form.description.label}

          </div>
          <div class="content">
            {$form.description.html}
          </div>
        </div>

        <div class="crm-section">
          <div class="label">
            {$form.note.label}

          </div>
          <div class="content">
              {$form.note.html}
        </div>
        <div class="crm-section">
          <div class="label">
           {$form.enp.label}

          </div>
          <div class="content">
            {$form.enp.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
           {$form.fnp.label}

          </div>
          <div class="content">
            {$form.fnp.html}
          </div>
        </div>
        </div>
      </fieldset>
      {include file="CRM/Booking/Form/Booking/Common.tpl"}
  <div id="crm-booking-dialog" class="crm-container"></div>
</div>
<div class="clear"></div>
{include file="CRM/common/formButtons.tpl" location="bottom"}
{literal}
<script type="text/javascript">
cj(function($) {
  CRM.BookingApp.start();
});
</script>
{/literal}
{/crmScope}
