<div id="main-container" class="crm-form-block">
  <div id="resource-main" >

  </div>
  <div id="crm-booking-dialog" class="crm-container"></div>
</div>
<div class="clear"></div>

<div>
<div class="hide">{$form.resources.html}</div>
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
cj(function() {
  CRM.BookingApp.start();
});
</script>
{/literal}
