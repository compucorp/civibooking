<script id="booking-info-contact-template" type="text/template">
    <div class="label">
        {$form.primary_contact_id.label}
      </div>
      <div class="content">
        {$form.primary_contact_id.html}
        {ts}OR{/ts} <select class="crm-booking-create-contact-select">
        <option value="">{ts}- create new contact -{/ts}</option>
        {crmAPI var='UFGroup' entity='UFGroup' action='get' is_active=1 is_reserved=1}
        {foreach from=$UFGroup.values item=profile}
          {if $profile.name eq 'new_individual' or $profile.name eq 'new_organization' or $profile.name eq 'new_household'}
            <option value="{$profile.id}">{$profile.title}</option>
          {/if}
        {/foreach}
      </select>
    </div>
</script>


<script id="booking-info-organisation-template" type="text/template">
    <div class="label">
        {$form.secondary_contact_id.label}
      </div>
      <div class="content">
        {$form.secondary_contact_id.html}
        {ts}OR{/ts} <select class="crm-booking-create-contact-select">
        <option value="">{ts}- create new contact -{/ts}</option>
        {crmAPI var='UFGroup' entity='UFGroup' action='get' is_active=1 is_reserved=1}
        {foreach from=$UFGroup.values item=profile}
          {if $profile.name eq 'new_individual' or $profile.name eq 'new_organization' or $profile.name eq 'new_household'}
            <option value="{$profile.id}">{$profile.title}</option>
          {/if}
        {/foreach}
      </select>
    </div>
</script>

<script id="booking-info-profile-template" type="text/template">
  <div id="crm-booking-profile-form" class="crm-container">
    <div class="crm-loading-element">{ts}Loading ... {/ts}</div>
  </div>

</script>



