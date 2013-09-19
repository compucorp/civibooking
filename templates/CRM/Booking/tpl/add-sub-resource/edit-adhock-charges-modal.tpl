<script id="edit-adhoc-charges-template" type="text/template">
<form>
  {foreach from=$items item=item}
    <div class="crm-section">
      <div class="label">
        <label for="{$item.name}">{$item.label}</label>
      </div>
       <div class="content">
        <input type="text" value="{$item.price}" disabled/> &times; <input type="text"/> &#61; {$currencySymbols}<span id="{$item.name}">0</span>
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
      <textarea name="note" rows="4" cols="50">
      </textarea>
  </div>
    <div class="clear"></div>
  </div>
  <div class="crm-submit-buttons">
    <span class="crm-button crm-button-type-next">
      <input class="validate form-submit default form-save" name="" value="{ts}Update ad-hoc charges{/ts}" type="submit" id="add-to-basket">
    </span>
  </div>
</form>
</script>
