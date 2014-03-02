<script id="add-sub-resource-template" type="text/template">
<div id="loading" class="crm-loading-element">{ts}Loading ...{/ts}</div>
<div id="content" class="hiddenElement">
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
      <span id="config-loading" class="crm-loading-element hiddenElement"></span>
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
      <input type="text"  name="required_date" id="required_date" size="10px" />
      <input type="text"  name="required_time"   id="required_time" size="4px" />
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="note">{ts}Note{/ts}</label>
    </div>
    <div class="content">
      <textarea name="note" rows="4" cols="50" id="sub-resource-note"></textarea>
  </div>
    <div class="clear"></div>
  </div>

   <div class="crm-section">
    <div class="label">
      {ts}Price estimate{/ts}
    </div>
    <div class="content">
      {$currencySymbols}<span id="price-estimate">0.00</span>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-submit-buttons">
    <span class="crm-button crm-button-type-next">
      <input class="validate form-submit default form-save" name="" value="{ts}Submit{/ts}" type="submit" id="add-to-basket">
    </span>
  </div>
</form>
</div>
</script>


