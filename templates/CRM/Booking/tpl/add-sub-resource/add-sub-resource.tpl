{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
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
            <td class="crm-booking-resource-to ">{$resource.start_date|crmDate}</td>
            <td class="crm-booking-resource-from ">{$resource.end_date|crmDate}</td>
            <td class="crm-booking-resource-price ">{$currencySymbols}<span data-ref="{$key}" id="resource-price-{$key}">{$resource.price}</span></td>
            <td class="crm-booking-resource-total-price ">{$currencySymbols}<span data-ref="{$key}" data-price="{$resource.price}" id="resource-total-price-{$key}">{$resource.price}<span></td>
            <td >
              <span><a href="#" data-ref="{$key}" data-sdate="{$resource.start_date}" data-edate="{$resource.end_date}" class="add-sub-resource action-item action-item-first" title="Add unlimited resource">{ts}Add unlimited resource{/ts}</a></span></td>
          </tr>
          <tr class="hiddenElement" id="crm-booking-sub-resource-row-{$key}">
            <td ></td>
            <td colspan=2 >
                <table cborder=1 id="crm-booking-sub-resource-table-{$key}">
                <thead>
                  <tr>
                    <th rowspan="1" colspan="1">{ts}Unlimited Resource Label{/ts}</th>
                    <th rowspan="1" colspan="1">{ts}Configuration{/ts}</th>
                    <th rowspan="1" colspan="1">{ts}Quantity{/ts}</th>
                    <th rowspan="1" colspan="1">{ts}Time Required{/ts}</th>
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
            <td>{$currencySymbols}<span id="sub-total-summary">{$subTotal}</span></td>
            <td></td>
          </tr>
          <tr >
            <td class="text-right"colspan="4"><span>{ts}Additional charges{/ts}: </span></td>
            <td>{$currencySymbols}<span id="ad-hoc-charge-summary">0</span></td>
            <td><span><a href="#" class="edit-adhoc-charge " title="{ts}Edit additional charges{/ts}">{ts}Edit additional charges{/ts}</a></span></td>
          </tr>
          <tr >
            <td class="text-right" colspan="4"><span>{$form.discount_amount_dummy.label}:</span></td>
            <td>{$currencySymbols}{$form.discount_amount_dummy.html}</td>
            <td></td>
        </td>
          <tr>
            <td class="text-right" colspan="4"><span>{ts}Total price{/ts}:</span></td>
            <td>{$currencySymbols}<span id="total-price-summary">{$totalPrice} </span></td>
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
    <td><%= moment(time_required,"YYYY-M-D HH:mm").strftime(crmDateFormat) %></td>
    <td>{$currencySymbols}<%= price_estimate %></td>
    <td>
        <span><a href="#" data-ref="<%=ref_id %>" data-parent-ref="<%= parent_ref_id %>" data-time-required="<%= time_required %>"  class="edit-sub-resource action-item action-item-first" >{ts}Edit{/ts}</a></span>
        <span><a href="#" data-ref="<%=ref_id %>" data-parent-ref="<%= parent_ref_id %>" data-price="<%= price_estimate %>"  class="remove-sub-resource action-item action-item-first" >{ts}Remove{/ts}</a></span>
    </td>
  </tr>
</script>
 {literal}
 <script type="text/javascript">
var timeConfig = "{/literal}{$timeconfig}{literal}";
</script>
 {/literal}
{/crmScope}
