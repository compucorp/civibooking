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


{* Search form and results for resources *}
<div id="brs-container" class="brs-container">
  {*
    <div class="crm-form-block crm-resorce-search-form-block">
      <div class="crm-accordion-wrapper crm-custom_search_form-accordion {if $rows}collapsed{/if}">
        <div class="crm-accordion-header crm-master-accordion-header">{ts}Check resource avaliability{/ts}</div><!-- /.crm-accordion-header -->
        <div id="search-form" class="crm-accordion-body">

        </div><!-- /.crm-accordion-body -->
      </div><!-- /.crm-accordion-wrapper -->
    </div><!-- /.crm-form-block -->

  *}

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

  <div id="basket-region"class="crm-container crm-form-block">
      <div class="">{$form.resources.html}</div>
      <div class="crm-section" >
        <h4>Currently in basket</h4>
        <table id="basket-table" class="hide">
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
{crmScript ext=uk.co.compucorp.civicrm.booking file=js/CRM/Booking/Form/SelectResource.js}

{literal}
<script type="text/javascript">
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

  cj().bookingscheduler({
    elements: elements,
    url: CRM.url('civicrm/booking/ajax/slots'),
    loadMode: 'day',
    date: new Date(),
  });

});
</script>
{/literal}

