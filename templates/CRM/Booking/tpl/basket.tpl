 {literal}
   <script type="text/template" id="selectedResourceRowTemplate">
    <tr data-eid="<%= data.id %>">
      <td><%= data.label %></td>
      <td><%= data.start_date %></td>
      <td><%= data.end_date %></td>
      <td><%= data.price %></td>
      <td><input type="button" value="Remove from basket" name="button" onclick="removeFromBasket(<%= data.id %>)"></td>
      </tr>
    </script>
{/literal}