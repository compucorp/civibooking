{* HEADER *}


<div class="crm-block crm-content-block crm-booking-view-form-block">
<h3>{ts}Day View{/ts}{$id}</h3>
</div>

<div class="crm-block crm-form-block crm-booking-search-form-block">
	<div class="label">{$form.dayview_select_date.label}</div>
    <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=dayview_select_date}</div>
    {$form.buttons.html}
</div>



{* FOOTER *}
<div class="crm-submit-buttons">
</div>
