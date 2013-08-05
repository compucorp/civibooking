<script id="search-form-template" type="text/template"> 
  <div class="crm-section">
    <div class="label">
      <label for="resource_id">Resource Id</label>
    </div>
    <div class="content">
      <input name="resource_id" type="text" id="resource_id" class="form-text">
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="contact_type">Resource type</label>
    </div>
    <div class="content">
     <select name="resource_type" id="resource_type" class="form-select">
        <option value="">- select resource type -</option>
        {foreach from=$resourceTypes  key=k  item=type}
        <option value="{$k}">{$type}</option>  
        {/foreach}
     </select>
    </div>
     <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label"></div>
    <div class="content">
       <input class="form-submit default" name="" value="Search" type="submit" id="search_form">
    </span>
    </div>
    <div class="clear"></div>
  </div>
</script>
