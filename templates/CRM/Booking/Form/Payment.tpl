<fieldset id="payment-detail" class="hiddenElement"><legend>{ts}Payment details{/ts}</legend>
        <div class="crm-section">
          <div class="label">
           {$form.select_payment_contact.label}

          </div>
          <div class="content">
            {$form.select_payment_contact.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
           {ts}Amount{/ts}

          </div>
          <div class="content">
            {$currencySymbols}{$totalAmount} <br/>
              <span class="description"> {ts}Booking payment amount. A contribution record will be created for this amount.{/ts} </span>
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.financial_type_id.label}

          </div>
          <div class="content">
            {$form.financial_type_id.html}
            <br/>
            <span class="description">{ts}Select the appropriate financial type for this payment.{/ts}</span>

          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.receive_date.label}

          </div>
          <div class="content">
            {include file="CRM/common/jcalendar.tpl" elementName=receive_date}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.payment_instrument_id.label}

          </div>
          <div class="content">
            {$form.payment_instrument_id.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.trxn_id.label}

          </div>
          <div class="content">
            {$form.trxn_id.html}
          </div>
        </div>
        <div class="crm-section">
          <div class="label">
            {$form.contribution_status_id.label}

          </div>
          <div class="content">
            {$form.contribution_status_id.html}
          </div>
        </div>
      </fieldset>
