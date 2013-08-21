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
      <label for="configurations">Configuration</label>
    </div>
    <div class="content">
    <select name="configurations" id="configSelect" class="form-select" disabled>
      <option value="">- select configuration -</option>
    </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="resource_id">Quantity</label>
    </div>
    <div class="content">
      <input name="resource_id" type="text" id="resource_id" class="form-text">
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="resource_id">Time required</label>
    </div>
    <div class="content">
      <input name="resource_id" type="text" id="resource_id" class="form-text">
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="resource_id">Note</label>
    </div>
    <div class="content">
      <textarea rows="4" cols="50">
      </textarea>
  </div>
    <div class="clear"></div>
  </div>

   <div class="crm-section">
    <div class="label">
      <label for="resource_id">Price estimate</label>
    </div>
    <div class="content">

  </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label"></div>
     <div class="content">
      <input class="form-submit default" name="" value="Add to basket" type="submit" id="add-to-basket">
      <input class="form-submit default" name="" value="Cancel" type="submit" id="cancel">
    </div>
    <div class="clear"></div>
  </div>
</script>
