<h1>Day view for: {$dayview_select_date|crmDate}</h1>
  {foreach from=$resources item=resource}
  <h3>{$resource.label}</h3>
    <table class="selector">
      <thead class="sticky">
        <tr>
          <th scope="col">{ts}From{/ts}</th>
          <th scope="col">{ts}To{/ts}</th>
          <th scope="col">{ts}Configuration{/ts}</th>
          <th scope="col">{ts}Booking ID{/ts}</th>
          <th scope="col">{ts}Function Title{/ts}</th>
          <th scope="col">{ts}Primary Contact{/ts}</th>
          <th scope="col">{ts}Secondary Contact{/ts}</th>
          <th scope="col">{ts}Commment{/ts}</th>
        </tr>
      </thead>
    {foreach from=$resource.slot item=resItem}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$resItem.start|crmDate}</td>
        <td>{$resItem.end|crmDate}</td>
        <td>{$resItem.resource_config_label}</td>
        <td>{$resItem.booking_id}</td>
        <td>{$resItem.function_title}</td>
        <td>{$resItem.primary_contact}</td>
        <td>{$resItem.secondary_contact}</td>
        <td>{$resItem.comment}</td>
      </tr>
    {/foreach}
    </table>
  {if $resource.subslot}
    <h5>{ts}Unlimited resources{/ts}</h5>
  <table class="selector">
    <thead class="sticky">
      <tr>
        <th scope="col">{ts}Label{/ts}</th>
        <th scope="col">{ts}Deliver at{/ts}</th>
        <th scope="col">{ts}Configuration{/ts}</th>
        <th scope="col">{ts}Quantity{/ts}</th>
        <th scope="col">{ts}Booking ID{/ts}</th>
        <th scope="col">{ts}Primary Contact{/ts}</th>
        <th scope="col">{ts}Secondary Contact{/ts}</th>
        <th scope="col">{ts}Comment{/ts}</th>
      </tr>
    </thead>
    {foreach from=$resource.subslot item=ssItem}
    <tr class="{cycle values="odd-row,even-row"}">
      <td>{$ssItem.sub_resource_label}</td>
      <td>{$ssItem.time_required|crmDate}</td>
      <td>{$ssItem.sub_resource_config_label}</td>
      <td>{$ssItem.quantity}</td>
      <td>{$ssItem.booking_id}</td>
      <td>{$ssItem.primary_contact}</td>
      <td>{$ssItem.secondary_contact}</td>
      <td>{$ssItem.comment}</td>
    </tr>
    {/foreach}
  </table>
  {/if}
  {/foreach}
