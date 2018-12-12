{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
<script id="edit-adhoc-charges-template" type="text/template">
{if $items}
<form>
  {foreach from=$items item=item}
    <div class="crm-section">
      <div class="label">
        <label for="{$item.name}">{$item.label}</label>
      </div>
       <div class="content">
        <input type="text" size="5px" value="{$item.price}" disabled/> &times; <input name="{$item.name}" data-id="{$item.id}" data-price="{$item.price}" type="text" size="5px" class="item" /> &#61; {$currencySymbols}<span id="{$item.name}">0</span>
      </div>
      <div class="clear"></div>
    </div>
  {/foreach}
  <div class="crm-section">
      <div class="label">
        {ts}Total{/ts}
      </div>
       <div class="content">
        {$currencySymbols}<span id="total-adhoc-charges">0</span>
      </div>
      <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">
      <label for="note">{ts}Note{/ts}</label>
    </div>
    <div class="content">
      <textarea id="adhoc-charges-note" name="note" rows="4" cols="50"></textarea>
  </div>
    <div class="clear"></div>
  </div>
  <div class="crm-submit-buttons">
    <span class="crm-button crm-button-type-next">
      <input class="validate form-submit default form-save" value="{ts}Update ad-hoc charges{/ts}" type="submit" id="update-adhoc-charges">
    </span>
  </div>
</form>
 {else}
    {capture assign=ftUrl}{crmURL p='civicrm/admin/adhoc_charges_item' q="reset=1&action=add"}{/capture}
    {ts 1=$ftUrl}There are no additional charges. <a href='%1'>Click here</a> if you want to add new additional charges your site.{/ts}
{/if}
</script>
{/crmScope}
