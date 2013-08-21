<script id="select-option-template" type="text/template">
  {literal}
  <option value=""><%= first_option %></option>
  <% _.each(options, function (option){ %>
   <option value="<%= option.id %>"><%= option.label %></option>
  <% }); %>
  {/literal}
</script>


