<script id="resource-table-template" type="text/template">
    <div id="resources">
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
              <span><a href="#" class="add-sub-resource action-item action-item-first" title="Add sub resource">{ts}Add sub resource{/ts}</a></span></td>
          </tr>
          <tr class="hiddenElement even-row" id="crm-booking-sub-resource-row-{$key}">
          <td ></div>
          <td colspan=2 class="">
              <table class="" border=1>
        <thead>
          <tr>
            <th rowspan="1" colspan="1">{ts}Sub resource label{/ts}</th>
            <th rowspan="1" colspan="1">{ts}Configuration{/ts}</th>
            <th rowspan="1" colspan="1">{ts}Quality{/ts}</th>
            <th rowspan="1" colspan="1">{ts}Price{/ts}</th>
          </tr>
        </thead>
                <tbody>
                  <tr>
                    <td>Label 1</td>
                    <td>2</td>
                    <td>1</td>
                    <td>£600</td>
                  </tr>
                   <tr>
                    <td>Label 2</td>
                    <td>2</td>
                    <td>1</td>
                    <td>£600</td>
                  </tr>
               </tbody>
             </table>
            </td>
          </tr>
        {/foreach}
         <tr>
            <td class="text-right" colspan="4"><span>Sub total: </span></td>
            <td>{$currencySymbols} <span id="sub-total-summary">{$subtotal}</td>
            <td></td>
          </tr>
          <tr >
            <td class="text-right"colspan="4"><span>{ts}Ad-hoc charges{ts}: </span></td>
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
