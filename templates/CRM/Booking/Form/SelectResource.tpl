{crmScope extensionKey=uk.co.compucorp.civicrm.booking} 
{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
 <div class="crm-container">

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
{if $resources}
  <div id="brs-container">
   <div id="scheduler">
        <div id="resource_scheduler" class="dhx_cal_container" style='width:100%; height:600px;'>
          <div class="dhx_cal_navline">
            <div class="dhx_cal_prev_button">&nbsp;</div>
            <div class="dhx_cal_next_button">&nbsp;</div>
            <div class="dhx_cal_today_button"></div>
            <div class="dhx_cal_date"></div>
            {* <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
            <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
            <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
            <div class="dhx_cal_tab" name="timeline_tab" style="right:204;"></div> *}
            <div class="dhx_minical_icon" id="dhx_minical_icon">&nbsp;</div>
          </div>
          <div class="dhx_cal_header">
          </div>
          <div class="dhx_cal_data">
          </div>
        </div>
       </div>

    <div id="basket-region"class="crm-container crm-form-block hiddenElement">
        <div class="hiddenElement">{$form.resources.html}</div>
        <div class="crm-section" >
          <h4>Currently in basket</h4>
          <table id="basket-table" >
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th></th>
                 </tr>
            </thead>
            <tbody>

            </tbody>

            <tfoot>
              <tr >
                <td colspan="5"></td>
              </tr>
              <tr >
                <td colspan="3" style="text-align:right">Sub total</td>
                <td >{$currencySymbols}<span id="subTotal"><span></td>
              </tr>
            </tfoot>
        </table>
      </div>
      <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
    </div>
  </div>
  <div id="crm-booking-new-slot" class="crm-container hiddenElement">

  </div>
  {literal}
  <script type="text/javascript">
  var crmDateFormat = "{/literal}{$dateFormat}{literal}";   //retrieve crmDateFormat
  
  var bookingId = "{/literal}{$bookingId}{literal}";
  var bookingSlotDate  = "{/literal}{$bookingSlotDate}{literal}";
  var newSlotcolour = "{/literal}{$colour}{literal}";

  cj(function($) {
    var elements = [// original hierarhical array to display
        {/literal}
          {foreach from=$resources key=key item=resourceType}
           {literal}
              {key:"{/literal}{$key}{literal}",label:"{/literal}{$resourceType.label|escape:'html'}{literal}", open: true, children: [
                {/literal}
                  {foreach from=$resourceType.child item=resource}
                   {literal}
                    {key:"{/literal}{$resource.id}{literal}", label:"{/literal}{$resource.label|escape:'html'}{literal}"},
                   {/literal}
                  {/foreach}
                  {literal}
               ]},
            {/literal}
          {/foreach}
        {literal}
    ];

    scheduler.createTimelineView({
      section_autoheight: false,
      name: "timeline",
      x_unit: "minute",
      x_date: "%H:%i",
      x_step: {/literal}{$xStep}{literal}, //time period
      x_size: {/literal}{$xSize}{literal}, // side of block from start time to end time
      x_start: {/literal}{$xStart}{literal},
      x_length: 48,
      y_unit: elements,
      y_property: "resource_id",
      render: "tree",
      folder_dy:20,
      dy:60
    });


  });
  </script>
  {/literal}
  {crmScript ext=uk.co.compucorp.civicrm.booking file=templates/CRM/Booking/Form/SelectResource.js}
{else}
  {capture assign=ftUrl}{crmURL p='civicrm/admin/resource' q="reset=1&action=add"}{/capture}
  {ts 1=$ftUrl}There are no resources to display. <a href='%1'>Click here</a> if you want to add new resources your site.{/ts}
{/if}

</div>

{/crmScope}
