<script id="select-config-option-template" type="text/template">
  {literal}
  <option value=""><%= first_option %></option>
  <% _.each(options, function (option){ %>
   <option  <%= option.selected %> data-maxsize="<%= option.max_size %>" data-price="<%= option.price %>" value="<%= option.id %>"><%= option.label %> - {/literal}{$currencySymbols}{literal}<%= option.price %> / <%= option['api.option_value.get'].values[0].label %></option>
  <% }); %>
  {/literal}
</script>


<script id="select-option-template" type="text/template">
  {literal}
  <option value=""><%= first_option %></option>
  <% _.each(options, function (option){ %>
   <option value="<%= option.id %>"><%= option.label %></option>
  <% }); %>
  {/literal}
</script>
