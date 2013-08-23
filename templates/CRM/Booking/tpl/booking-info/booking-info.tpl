<script id="booking-info-contact-template" type="text/template">
    <div class="label">
        {$form.contact.label}
      </div>
      <div class="content">
        {$form.contact.html}
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
        {$form.organisation.label}
      </div>
      <div class="content">
        {$form.organisation.html}
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



