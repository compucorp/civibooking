{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
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
{if $resources}
{if empty($resources)}
{* No matches for submitted search request or viewing an empty result. *}
<div class="messages status no-popup">
  <div class="icon inform-icon"></div>&nbsp;
    {ts}No matches found.{/ts}
    <p>
		{ts}Booking Date={/ts} {$dayview_select_date}
		</p>
    {ts}Suggestions:{/ts}
    <ul>
    <li>{ts}Try a different date{/ts}</li>
    </ul>
</div>
{else}
 {include file="CRM/Booking/Form/Search/DayViewResults.tpl"}
{/if}
{/if}
<div class="crm-submit-buttons">
</div>
{literal}
<script type="text/javascript">
cj(function() {
   cj().crmAccordions();
});
</script>
{/literal}
{/crmScope}
