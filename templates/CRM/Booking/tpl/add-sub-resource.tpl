<script id="resource-table-template" type="text/template">
    <div id="resources">
      <table id="" class="display">
        <thead>
          <tr>
            <th class="" rowspan="1" colspan="1">Resource label</th>
            <th class="" rowspan="1" colspan="1">From</th>
            <th class="" rowspan="1" colspan="1">To</th>
            <th class="" rowspan="1" colspan="1">Price</th>
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
            <td >
              <span><a href="#" class="add-sub-resource action-item action-item-first" title="Add sub resource">Add sub resource</a></span></td>
          </tr>
          <tr class="hiddenElement even-row" id="crm-booking-sub-resource-row-{$key}">
          <td colspan=1 class=""></div>

          <td colspan=4 class="">
              Sub resource detail

              <table class="display">
              <thead>
                <tr>
                  <th class="" rowspan="1" colspan="1"></th>
                  <th class="" rowspan="1" colspan="1">Sub resource label</th>
                  <th class="" rowspan="1" colspan="1">To</th>
                  <th class="" rowspan="1" colspan="1">Price</th>
                  <th class="" rowspan="1" colspan="1"></th>
                </tr>
              </thead>
                <tbody>
                  <tr>
                    <td class="label">Sub resource 1</td>
                    <td></td>
                  </tr>
                  <tr>
                   <td class="label">Config</td>
                   <td></td>
                  </tr>
               </tbody>
             </table>
            </td>
          </tr>
        {/foreach}
         <tr  class="">
            <td colspan="3"><span>Sub total: </span></td>
            <td>{$currencySymbols} <span id="sub-total-summary">{$subtotal}</td>
            <td></td>
          </tr>
          <tr  class="">
            <td colspan="3"><span>Ad-hock charge: </span></td>
            <td>{$currencySymbols} <span id="ad-hock-charge-summary">0</td>
            <td></td>
          </tr>
          <tr  class="">
            <td colspan="3"><span>Manual adjustment:</span></td>
            <td>{$currencySymbols} <input type="text" name="manual-adjustment" size=2 value="0"/></td>
            <td><span><a href="#" class="edit-adhoc-charge " title="Add ad-hoc charge">Edit ad-hock charges</a></span></td>
</td>
          <tr  class="">
            <td colspan="3"><span>Total price:</span></td>
            <td>{$currencySymbols} <span id="total-price-summary"> {$totalPrice} </span></td>
          <td></td>
      </tbody>
      </table>
  </div>
</script>
