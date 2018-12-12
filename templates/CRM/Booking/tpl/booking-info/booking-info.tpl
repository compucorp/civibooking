{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
<script id="booking-info-contact-template" type="text/template">
    <div class="label">
        {$form.primary_contact_id.label}
      </div>
      <div class="content">
        {$form.primary_contact_id.html}
    </div>
</script>


<script id="booking-info-organisation-template" type="text/template">
    <div class="label">
        {$form.secondary_contact_id.label}
      </div>
      <div class="content">
        {$form.secondary_contact_id.html}
    </div>
</script>

<script id="booking-info-profile-template" type="text/template">
  <div id="crm-booking-profile-form" class="crm-container">
    <div class="crm-loading-element">{ts}Loading ... {/ts}</div>
  </div>

</script>



{/crmScope}
