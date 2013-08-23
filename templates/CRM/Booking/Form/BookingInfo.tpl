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
  <div id="booking-detail-container" >
    <div class="crm-form-block">
      {$form.contact_select_id.html}
      {$form.organisation_select_id.html}
      <div class="crm-section">
          <div class="label">
            {$form.contact.label}
          </label>
          </div>
          <div class="content">
              {$form.contact.html}
          </div>
      </div>
      <div class="crm-section">
          <div class="label">
            {$form.organisation.label}
          </label>
          </div>
          <div class="content">
              {$form.organisation.html}
          </div>
      </div>
      <div class="crm-section">
          <div class="label">
            {$form.booking_status.label}
          </label>
          </div>
          <div class="content">
              {$form.booking_status.html}
          </div>
      </div>
      <div class="crm-section">
          <div class="label">
            {$form.po_no.label}
          </label>
          </div>
          <div class="content">
              {$form.po_no.html}
          </div>
      </div>
      <fieldset><legend>{ts}Booking information{/ts}</legend>
        <div class="crm-section">
          <div class="label">
            {$form.title.label}
          </label>
          </div>
          <div class="content">
              {$form.title.html}
          </div>
        </div>
         <div class="crm-section">
          <div class="label">
            {$form.event_start_date.label}
          </label>
          </div>
          <div class="content">
            {include file="CRM/common/jcalendar.tpl" elementName=event_start_date}
          </div>
        </div>
         <div class="crm-section">
          <div class="label">
           {$form.description.label}
          </label>
          </div>
          <div class="content">
            {$form.description.html}
          </div>
        </div>

        <div class="crm-section">
          <div class="label">
            {$form.note.label}
          </label>
          </div>
          <div class="content">
              {$form.note.html}
        </div>
        <div class="crm-section">
          <div class="label">
           {$form.enp.label}
          </label>
          </div>
          <div class="content">
            {$form.enp.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
           {$form.fnp.label}
          </label>
          </div>
          <div class="content">
            {$form.fnp.html}
          </div>
        </div>
        </div>
      </fieldset>
      <div class="crm-section">
          <div class="label">
           {$form.send_conformation.label}
          </label>
          </div>
          <div class="content">
            {$form.send_conformation.html}
          </div>
      </div>
      <fieldset><legend>{ts}Email booking conformation{/ts}</legend>
        <div class="crm-section">
          <div class="label">
           {$form.email_to.label}
          </label>
          </div>
          <div class="content">
            {$form.email_to.html}
          </div>
        </div>
      </fieldset>
       <div class="crm-section">
          <div class="label">
           {ts}Payment status{/ts}
          </label>
          </div>
          <div class="content">
            {ts}Unpaid{/ts}
          </div>
      </div>
      <div class="crm-section">
          <div class="label">
           {$form.record_contribution.label}
          </label>
          </div>
          <div class="content">
            {$form.record_contribution.html}
          </div>
      </div>
      <fieldset><legend>{ts}Payment details{/ts}</legend>
        <div class="crm-section">
          <div class="label">
           {$form.select_payment_contact.label}
          </label>
          </div>
          <div class="content">
            {$form.select_payment_contact.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
           {ts}Amount{/ts}
          </label>
          </div>
          <div class="content">
            {$currencySymbols} {$amount} <br/>
              <span class="description"> {ts}Booking payment amount. A contribution record will be created for this amount.{/ts} </span>
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.financial_type_id.label}
          </label>
          </div>
          <div class="content">
            {$form.financial_type_id.label}
            <br/>
            <span class="description">{ts}Select the appropriate financial type for this payment.{/ts}</span>

          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.receive_date.label}
          </label>
          </div>
          <div class="content">
            {include file="CRM/common/jcalendar.tpl" elementName=receive_date}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.payment_instrument_id.label}
          </label>
          </div>
          <div class="content">
            {$form.payment_instrument_id.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.trxn_id.label}
          </label>
          </div>
          <div class="content">
            {$form.trxn_id.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.contribution_status_id.label}
          </label>
          </div>
          <div class="content">
            {$form.contribution_status_id.html}
          </div>
        </div>
      </fieldset>
    </div>
  <div id="crm-booking-dialog" class="crm-container"></div>
</div>
<div class="clear"></div>
{include file="CRM/common/formButtons.tpl" location="bottom"}
{literal}
<script type="text/javascript">
cj(function() {
  //CRM.BookingApp.start();
});
</script>
{/literal}
