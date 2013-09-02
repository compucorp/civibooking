<script id="add-resource-template" type="text/template">
<form id="add-resource-form">
   <input type="hidden" name="resource-label" id="resource-label" autofocus/>
  <div class="crm-section">
    <div class="label">
      <label>{ts}Start date time{/ts}</label>
    </div>
    <div class="content">
      <select id="start-day-select" name="start-day-select" class="crm-booking-form-add-resource">
        {foreach from=$days item=day}
          <option value="{$day}">{$day}</option>
        {/foreach}
     </select>
     <select id="start-month-select" name="start-month-select" class="crm-booking-form-add-resource">
        {foreach from=$months key=k item=month}
          <option value="{$k}">{$month}</option>
        {/foreach}
     </select>
     <select id="start-year-select" name="start-year-select" class="crm-booking-form-add-resource">
        {foreach from=$years item=year}
          <option value="{$year}">{$year}</option>
        {/foreach}
     </select>
      <select id="start-time-select" name="start-time-select" class="crm-booking-form-add-resource">
        {foreach from=$timeOptions key=k item=time}
          <option value="{$time.time}">{$time.time}</option>
        {/foreach}
     </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label>{ts}End date time{/ts}</label>
    </div>
    <div class="content">
     <select id="end-day-select" name="end-day-select" class="crm-booking-form-add-resource">
        {foreach from=$days item=day}
          <option value="{$day}">{$day}</option>
        {/foreach}
     </select>
     <select id="end-month-select" name="end-month-select" class="crm-booking-form-add-resource">
        {foreach from=$months key=k item=month}
          <option value="{$k}">{$month}</option>
        {/foreach}
     </select>
     <select id="end-year-select" name="end-year-select" class="crm-booking-form-add-resource">
        {foreach from=$years item=year}
          <option value="{$year}">{$year}</option>
        {/foreach}
     </select>
     <select id="end-time-select" name="end-time-select" class="crm-booking-form-add-resource">
        {foreach from=$timeOptions key=k item=time}
          <option value="{$time.time}">{$time.time}</option>
      {/foreach}
      </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="configuration">{ts}Configuration{/ts}</label>
    </div>
    <div class="content">
    <select name="configuration" id="configSelect" class="crm-booking-form-add-resource">
    </select>
    <span id="config-max-size"></span>
    </div>
    <div class="clear"></div>
   </div>
   <div class="crm-section">
    <div class="label">
      <label for="quantity">{ts}Quantity{/ts}</label>
    </div>
    <div class="content">
      <input type="text" name="quantity" class="crm-booking-form-add-resource"/>
    </div>
    <div class="clear"></div>

   </div>

   <div class="crm-section">
      <div class="label">
        <label for="note">{ts}Note{/ts}</label>
      </div>
      <div class="content">
        <textarea id="note" class="crm-booking-form-add-resource"></textarea>
      </div>
      <div class="clear"></div>
    </div>

   <div class="crm-section">
      <div class="label">
        <label >{ts}Price estimate{/ts}</label>
      </div>
      <div class="content">
         {$currencySymbols}<span id="price-estimate">0</span>
      </div>
      <div class="clear"></div>
    </div>

    <div id="add-resource-btn" class="crm-submit-buttons"  >
    <span class="crm-button crm-button-type-next">
      <input class="validate form-submit default" name="select-resource-save" value="{ts}Save{/ts}" type="submit"  >
      <input class="validate form-submit default" name="select-resource-cancel" value="{ts}Cancel{/ts}" type="submit" >
    </span>
  </div>
</form>
</script>



