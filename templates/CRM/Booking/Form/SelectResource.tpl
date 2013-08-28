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
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
<div id="brs-container">
 <div id="scheduler">
      <div id="resource_scheduler" class="dhx_cal_container" style='width:100%; height:500px;'>
        <div class="dhx_cal_navline">
          <div class="dhx_cal_prev_button">&nbsp;</div>
          <div class="dhx_cal_next_button">&nbsp;</div>
          <div class="dhx_cal_today_button"></div>
          <div class="dhx_cal_date"></div>
          {* <div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
          <div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
          <div class="dhx_cal_tab" name="timeline_tab" style="right:204;"></div>
          <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div> *}
          <div class="dhx_minical_icon" id="dhx_minical_icon" onclick="show_minical()">&nbsp;</div>
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
          <tr>
              <th>Resource</th>
              <th>From</th>
              <th>To</th>
              <th>Price</th>
              <th></th>
           </tr>
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
  <input type="hidden" name="resource-label" id="resource-label" autofocus/>
  <div class="crm-section">
    <div class="label">
      <label>{ts}Start date time{/ts}</label>
    </div>
    <div class="content">
      <select id="start-day-select" name="start-day-select" class="crm-booking-form-add-resource">
        {foreach from=$days item=day}
          <option value="{$day}">{$day}</option>
        {/foreach}
     </select>
     <select id="start-month-select" name="start-month-select" class="crm-booking-form-add-resource">
        {foreach from=$months key=k item=month}
          <option value="{$k}">{$month}</option>
        {/foreach}
     </select>
     <select id="start-year-select" name="start-year-select" class="crm-booking-form-add-resource">
        {foreach from=$years item=year}
          <option value="{$year}">{$year}</option>
        {/foreach}
     </select>
      <select id="start-time-select" name="start-time-select" class="crm-booking-form-add-resource">
        {foreach from=$timeOptions key=k item=time}
          <option value="{$time.time}">{$time.time}</option>
        {/foreach}
     </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label>{ts}End date time{/ts}</label>
    </div>
    <div class="content">
     <select id="end-day-select" name="end-day-select" class="crm-booking-form-add-resource">
        {foreach from=$days item=day}
          <option value="{$day}">{$day}</option>
        {/foreach}
     </select>
     <select id="end-month-select" name="end-month-select" class="crm-booking-form-add-resource">
        {foreach from=$months key=k item=month}
          <option value="{$k}">{$month}</option>
        {/foreach}
     </select>
     <select id="end-year-select" name="end-year-select" class="crm-booking-form-add-resource">
        {foreach from=$years item=year}
          <option value="{$year}">{$year}</option>
        {/foreach}
     </select>
     <select id="end-time-select" name="end-time-select" class="crm-booking-form-add-resource">
        {foreach from=$timeOptions key=k item=time}
          <option value="{$time.time}">{$time.time}</option>
      {/foreach}
      </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">
      <label for="configuration">{ts}Configuration{/ts}</label>
    </div>
    <div class="content">
    <select name="configuration" id="configSelect" class="crm-booking-form-add-resource">
    </select>
    <span id="config-max-size"></span>
    </div>
    <div class="clear"></div>
   </div>
   <div class="crm-section">
    <div class="label">
      <label for="quantity">{ts}Quantity{/ts}</label>
    </div>
    <div class="content">
      <input type="text" name="quantity" class="crm-booking-form-add-resource"/>
    </div>
    <div class="clear"></div>

   </div>

   <div class="crm-section">
      <div class="label">
        <label for="note">{ts}Note{/ts}</label>
      </div>
      <div class="content">
        <textarea id="note" class="crm-booking-form-add-resource"></textarea>
      </div>
      <div class="clear"></div>
    </div>

   <div class="crm-section">
      <div class="label">
        <label >{ts}Price estimate{/ts}</label>
      </div>
      <div class="content">
         {$currencySymbols}<span id="price-estimate"></span>
      </div>
      <div class="clear"></div>
    </div>

    <div id="add-resource-btn" class="crm-submit-buttons"  >
    <span class="crm-button crm-button-type-next">
      <input class="validate form-submit default" name="select-resource-save" value="{ts}Save{/ts}" type="submit"  >
      <input class="validate form-submit default" name="select-resource-cancel" value="{ts}Cancel{/ts}" type="submit" >
    </span>
  </div>

</div>
{literal}
<script type="text/javascript">
var crmDateFormat = "{/literal} {$dateformat} {literal}";
cj(function($) {
  var elements = [// original hierarhical array to display
      {/literal}
        {foreach from=$resources key=key item=resourceType}
         {literal}
            {key:"{/literal}{$key}{literal}",label:"{/literal}{$resourceType.label}{literal}", open: true, children: [
              {/literal}
                {foreach from=$resourceType.child item=resource}
                 {literal}
                  {key:{/literal}{$resource.id}{literal}, label:"{/literal}{$resource.label}{literal}"},
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
    x_step: 30,
    x_size: 24,
    x_start: 16,
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
{crmScript ext=uk.co.compucorp.civicrm.booking file=js/CRM/Booking/Form/SelectResource.js}


