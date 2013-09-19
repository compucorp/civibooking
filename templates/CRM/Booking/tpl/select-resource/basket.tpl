 {literal}
   <script type="text/template" id="selected-resource-row-tpl">
    <tr data-eid="<%= data.id %>">
      <td><%= data.label %></td>
      <td><%= data.start_date %></td>
      <td><%= data.end_date %></td>
      <td>{/literal}{$currencySymbols}{literal}<%= data.price %></td>
      <td><input type="button" data-eid="<%= data.id %>" class="remove-from-basket-btn" value="{ts}Remove from basket{/ts}" name="button" ></td>
      </tr>
    </script>
{/literal}
