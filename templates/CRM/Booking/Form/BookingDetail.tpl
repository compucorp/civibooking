{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
<div id="main-container" class="crm-form-block">
  <div id="booking-detail-container" >

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
