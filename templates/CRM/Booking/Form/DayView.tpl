{* Search form and results for DayView *}

{* Search form *}
<div class="crm-form-block crm-search-form-block">
<div class="crm-accordion-wrapper crm-activity_search-accordion {if $rows}collapsed{/if}">
 <div class="crm-accordion-header crm-master-accordion-header">
   {ts}Edit Search{/ts}
</div><!-- /.crm-accordion-header -->
<div class="crm-accordion-body">
<div id="searchForm" class="crm-block crm-form-block crm-contact-custom-search-activity-search-form-block">
        <table class="form-layout-compressed">
                <tr class="crm-contact-custom-search-activity-search-form-block-{$element}">
                    <td class="label">{$form.dayview_select_date.label}</td>
                    <td>
                    {include file="CRM/common/jcalendar.tpl" elementName=dayview_select_date}
                    </td>
                </tr>
        </table>
      <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
</div><!-- /.crm-accordion-body -->
</div><!-- /.crm-accordion-wrapper -->
</div><!-- /.crm-form-block -->

{* Search DayView results *}
{if isset($resources)}

{if empty($resources)}
{* No matches for submitted search request or viewing an empty result. *}
<div class="messages status no-popup">
  <div class="icon inform-icon"></div>&nbsp;
            {ts}No matches found.{/ts}
            <p>
			{ts} Booking Date ={/ts} {$dayview_select_date}
			</p>
            {ts}Suggestions:{/ts}
            <ul>
            <li>{ts}try a different date{/ts}</li>
            </ul>
</div>

{else}
<h1>Day view for: {$dayview_select_date|crmDate}</h1>
	{foreach from=$resources item=resource}
	<h3>{$resource.label}</h3>
    <table class="selector">
      <thead class="sticky">
        <tr>
          <th scope="col">{ts}From{/ts}</th>
          <th scope="col">{ts}To{/ts}</th>
          <th scope="col">{ts}Configuration{/ts}</th>
          <th scope="col">{ts}Booking ID{/ts}</th>
          <th scope="col">{ts}Function Title{/ts}</th>
          <th scope="col">{ts}Primary Contact{/ts}</th>
          <th scope="col">{ts}Secondary Contact{/ts}</th>
          <th scope="col">{ts}Commment{/ts}</th>
        </tr>
      </thead>
		{foreach from=$resource.slot item=resItem}
      <tr class="{cycle values="odd-row,even-row"}">
        <td>{$resItem.start}</td>
        <td>{$resItem.end}</td>
        <td>{$resItem.resource_config_label}</td>
        <td>{$resItem.booking_id}</td>
        <td>{$resItem.function_title}</td>
        <td>{$resItem.primary_contact}</td>
        <td>{$resItem.secondary_contact}</td>
        <td>{$resItem.comment}</td>
      </tr>
		{/foreach}
    </table>
    <h5>{ts}Sub resources{/ts}</h5>
	<table class="selector">
		<thead class="sticky">
			<tr>
				<th scope="col">{ts}Label{/ts}</th>
				<th scope="col">{ts}Deliver at{/ts}</th>
				<th scope="col">{ts}Configuration{/ts}</th>
				<th scope="col">{ts}Quantity{/ts}</th>
				<th scope="col">{ts}Booking ID{/ts}</th>
				<th scope="col">{ts}Primary Contact{/ts}</th>
				<th scope="col">{ts}Secondary Contact{/ts}</th>
				<th scope="col">{ts}Comment{/ts}</th>
			</tr>
		</thead>
		{foreach from=$resource.subslot item=ssItem}
		<tr class="{cycle values="odd-row,even-row"}">
			<td>{$ssItem.sub_resource_label}</td>
			<td>{$ssItem.time_required}</td>
			<td>{$ssItem.sub_resource_config_label}</td>
			<td>{$ssItem.quantity}</td>
			<td>{$ssItem.booking_id}</td>
			<td>{$ssItem.primary_contact}</td>
			<td>{$ssItem.secondary_contact}</td>
			<td>{$ssItem.comment}</td>
		</tr>
		{/foreach}
	</table>
	{/foreach}
{/if}
{/if}
{* FOOTER *}
<div class="crm-submit-buttons">
</div>
{literal}
<script type="text/javascript">
cj(function() {
   cj().crmAccordions();
});
</script>
{/literal}
