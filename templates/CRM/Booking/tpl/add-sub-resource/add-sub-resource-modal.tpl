<script id="add-sub-resource-template" type="text/template">
  <div class="crm-section">
    <div class="label">
      <label for="resources">Select resource</label>
    </div>
    <div class="content">
    <select name="resources" id="resourceSelect" class="form-select">
    </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="configuration">Configuration</label>
    </div>
    <div class="content">
    <select name="configuration" id="configSelect" class="form-select" disabled>
      <option value="">- select configuration -</option>
    </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="quantity">Quantity</label>
    </div>
    <div class="content">
      <input name="quantity" type="text" id="quantity" class="form-text" disabled>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="time_required">Time required</label>
    </div>
    <div class="content">
      <input name="time_required" type="text" id="time_required" class="date form-text">
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="note">Note</label>
    </div>
    <div class="content">
      <textarea name="note" rows="4" cols="50">
      </textarea>
  </div>
    <div class="clear"></div>
  </div>

   <div class="crm-section">
    <div class="label">
      Price estimate
    </div>
    <div class="content">
      {$currencySymbols}<span id="price-estimate">0</span>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-submit-buttons" style="">
    <span class="crm-button crm-button-type-next crm-button_qf_Edit_next">
      <input class="validate form-submit default" name="" value="Add to basket" type="submit" id="add-to-basket">
    </span>
  </div>
</script>



