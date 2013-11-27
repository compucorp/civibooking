 {literal}
   <script type="text/template" id="selected-resource-row-tpl">
    <tr data-eid="<%= data.id %>" data-rid="<%= data.resource_id %>" data-update="<%= data.is_updated %>" >
      <td><%= data.label %></td>
      <td><%= moment(data.start_date).strftime(crmDateFormat) %></td>
      <td><%= moment(data.end_date).strftime(crmDateFormat) %></td>
      <td>{/literal}{$currencySymbols}{literal}<%= data.price %></td>
      <td><input type="button" data-eid="<%= data.id %>" class="remove-from-basket-btn" value="{/literal}{ts}Remove from basket{/ts}{literal}" name="button" ></td>
      </tr>
    </script>
{/literal}
