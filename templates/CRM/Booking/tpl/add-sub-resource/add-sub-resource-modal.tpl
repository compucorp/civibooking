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
      <select id="required-day-select" name="required-day-select">
        {foreach from=$days item=day}
          <option value="{$day}">{$day}</option>
        {/foreach}
     </select>
     <select id="required-month-select" name="required-month-select">
        {foreach from=$months key=k item=month}
          <option value="{$k}">{$month}</option>
        {/foreach}
     </select>
     <select id="required-year-select" name="required-year-select">
        {foreach from=$years item=year}
          <option value="{$year}">{$year}</option>
        {/foreach}
     </select>
      <select id="required-time-select" name="required-time-select" >
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
      <textarea name="note" rows="4" cols="50" id="sub-resource-note"></textarea>
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
      <input class="validate form-submit default form-save" name="" value="{ts}Submit{/ts}" type="submit" id="add-to-basket">
    </span>
  </div>
</form>
</div>
</script>



