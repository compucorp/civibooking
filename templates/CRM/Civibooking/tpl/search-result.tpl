<script id="search-result-table-template" type="text/template">
  {* <div id="reservations" class="ui-selectable">*}
  <table id="<%=result.date_timestamp%>" class="reservations" border="1" cellpadding="0" width="100%">
			<tr>
				<td class="resdate" width="3%"><%= result.date %></td>
					{literal}
					<% _.each(result.time_options, function (option){ %>
           	<td class="reslabel" width="<%= option.width %>%"><%= option.time %></td>
				 <% }); %>
				 <td width=2% class="reslabel"></td>
					{/literal}
			</tr>
			
		</table>

	{* </div> *}
</script>

<script id="search-result-row-template" type="text/template">
	{literal}
				<td colspan=1 class="resourcename" >
						<%= label %>
				 </td>
				 <% _.each(slots, function (slot){ %>
				 	<td colspan=<%=slot.period_span%> ref="" class="reservable clickres slot ui-selectee" ></td>
				 <% }); %>
				 <td colspan=1 class="ui-selectee" >
				    <button type="button" class="add-to-basket btn btn-default">Add to basket</button>
				 </td>

	{/literal}
</script>


