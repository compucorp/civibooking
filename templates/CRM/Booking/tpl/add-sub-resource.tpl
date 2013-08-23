<script id="resource-table-template" type="text/template">
    <div id="resources-container">
      <table id="" class="display">
        <thead>
          <tr>
            <th class="" rowspan="1" colspan="1">{ts}Resource label{/ts}</th>
            <th class="" rowspan="1" colspan="1">{ts}From{/ts}</th>
            <th class="" rowspan="1" colspan="1">{ts}To{/ts}</th>
            <th class="" rowspan="1" colspan="1">{ts}Price{/ts}</th>
            <th class="" rowspan="1" colspan="1">{ts}Total Price{/ts}</th>
            <th class="" rowspan="1" colspan="1"></th>
          </tr>
        </thead>
        <tbody >
          {foreach from=$resources key=key item=resource}
          <tr id="crm-booking-resource-row-{$key}" class="">
            <td class="">
              <a class="collapsed" href="#" data-ref="{$key}"></a>&nbsp;<strong>{$resource.label}</strong><br>
            </td>
            <td class="crm-booking-resource-to ">{$resource.start_date} </td>
            <td class="crm-booking-resource-from ">{$resource.end_date}</td>
            <td class="crm-booking-resource-price ">{$currencySymbols}{$resource.price}</td>
            <td class="crm-booking-resource-total-price ">{$currencySymbols}{$resource.price}</td>
            <td >
              <span><a href="#" data-ref="{$key}" class="add-sub-resource action-item action-item-first" title="Add sub resource">{ts}Add sub resource{/ts}</a></span></td>
          </tr>
          <tr class="hiddenElement" id="crm-booking-sub-resource-row-{$key}">
            <td ></td>
            <td colspan=2 >
                <table cborder=1 id="crm-booking-sub-resource-table-{$key}">
                <thead>
                  <tr>
                    <th rowspan="1" colspan="1">{ts}Sub resource label{/ts}</th>
                    <th rowspan="1" colspan="1">{ts}Configuration{/ts}</th>
                    <th rowspan="1" colspan="1">{ts}Quality{/ts}</th>
                    <th rowspan="1" colspan="1">{ts}Price{/ts}</th>
                    <th rowspan="1" colspan="1"></th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
               </table>
            </td>
          </tr>
        {/foreach}
         <tr>
            <td class="text-right" colspan="4"><span>{ts}Sub total{/ts}: </span></td>
            <td>{$currencySymbols} <span id="sub-total-summary">{$subtotal}</td>
            <td></td>
          </tr>
          <tr >
            <td class="text-right"colspan="4"><span>{ts}Ad-hoc charges{/ts}: </span></td>
            <td>{$currencySymbols} <span id="ad-hoc-charge-summary">0</td>
            <td></td>
          </tr>
          <tr >
            <td class="text-right" colspan="4"><span>{ts}Manual adjustment{/ts}:</span></td>
            <td>{$currencySymbols} <input type="text" name="manual-adjustment" size=10 value="0"/></td>
            <td><span><a href="#" class="edit-adhoc-charge " title="Add ad-hoc charge">{ts}Edit ad-hoc charges{/ts}</a></span></td>
        </td>
          <tr>
            <td class="text-right" colspan="4"><span>{ts}Total price{/ts}:</span></td>
            <td>{$currencySymbols} <span id="total-price-summary">{$totalPrice} </span></td>
          <td></td>
      </tbody>
      </table>
  </div>
</script>


<script id="sub-resource-row-template" type="text/template">
  <tr id="crm-booking-sub-resource-individual-row-<%= ref_id %>">
    <td><%= resource.label %></td>
    <td><%= configuration.label %></td>
    <td><%= quantity %></td>
    <td>{$currencySymbols} <%= price_estimate %></td>
    <td><span><a href="#" data-ref="<%=ref_id %>" class="remove-sub-resource action-item action-item-first" >{ts}Remove{/ts}</a></span></td>
  </tr>
</script>
