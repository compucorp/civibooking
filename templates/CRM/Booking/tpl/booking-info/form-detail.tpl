<script id="booking-info-template" type="text/template">
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
      <div id="email-booking-conformation" class="hiddenElement">
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
      </div>
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
      <div id="payment-detail" class="hiddenElement">
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
</script>

