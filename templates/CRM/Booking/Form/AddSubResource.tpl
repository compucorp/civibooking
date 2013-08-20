<div id="main-container" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
  <div id="resource-main" class="ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-1" role="tabpanel" aria-expanded="true" aria-hidden="false">

  </div>
  <div id="crm-booking-dialog" class="crm-container"></div>
</div>
<div class="clear"></div>

<div>

{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
<script type="text/javascript">
cj(function() {
  CRM.BookingApp.start();
});
</script>
{/literal}
