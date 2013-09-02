<script id="add-sub-resource-template" type="text/template">
<form>
  <div class="crm-section">
    <div class="label">
      <label for="resource_select">{ts}Select resource{/ts}</label>
    </div>
    <div class="content">
    <select name="resource_select" id="resource_select" class="form-select">
    </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="configuration_select">{ts}Configuration{/ts}</label>
    </div>
    <div class="content">
    <select name="configuration_select" id="configuration_select" class="form-select" disabled>
      <option value="">- {ts}select configuration{/ts} -</option>
    </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="quantity">{ts}Quantity{/ts}</label>
    </div>
    <div class="content">
      <input name="quantity" type="text" id="quantity" class="form-text" disabled>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="time_required">{ts}Time required{/ts}</label>
    </div>
    <div class="content">
      <select id="time_required" name="time_required" class="form-select">
        <option value="">- {ts}select time{/ts} -</option>
        {foreach from=$timeOptions key=k item=time}
          <option value="{$time.time}">{$time.time}</option>
        {/foreach}
     </select>
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

   <div class="crm-section">
    <div class="label">
      {ts}Price estimate{/ts}
    </div>
    <div class="content">
      {$currencySymbols}<span id="price-estimate">0</span>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-submit-buttons">
    <span class="crm-button crm-button-type-next">
      <input class="validate form-submit default form-save" name="" value="Add to basket" type="submit" id="add-to-basket">
    </span>
  </div>
</form>
</script>



