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
      <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:500px;'>
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

  <div id="basket-panel"class="crm-container crm-form-block">
      <div class="hide">{$form.resources.html}</div>
      <div class="crm-section" >
        <h4>Currently in basket</h4>
        <table id="resourceBasket" class="hide">
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


{literal}
<script type="text/javascript">
var basket = {};
var subTotal = 0;

cj(document).ready(function() {
   //cj().crmAccordions();
   
    scheduler.locale.labels.timeline_tab = "Timeline";
    scheduler.locale.labels.section_resource = "Resource";
    scheduler.locale.labels.section_resource_type = "Size";
    scheduler.locale.labels.section_resource_size = "Type";
    scheduler.locale.labels.section_configuration = "Configuration";
    scheduler.locale.labels.section_quality = "Quality";
    scheduler.locale.labels.section_note = "Note";
    scheduler.locale.labels.section_price_estimated = "Price Estimated";

    scheduler.config.show_loading = true;
    scheduler.config.full_day = true;
    scheduler.config.details_on_create=true;
    scheduler.config.details_on_dblclick=false;
    scheduler.config.collision_limit = 1; //allows creating 1 events per time slot

    scheduler.config.xml_date="%Y-%m-%d %H:%i";
    
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

 
    scheduler.attachEvent("onBeforeLightbox", function(eid) {
      scheduler.resetLightbox();
      var ev = scheduler.getEvent(eid);
      ev.text = '' + ev.id;      //todo GET config config from APS
      var rid = ev.resource_id;
      var config =[    
          { key:"description", label:"config1"},
          { key:"location",    label:"config2"},
          { key:"time",        label:"config3"}    
      ];
   
      scheduler.config.lightbox.sections=[ 
        {name:"time", height:72, type:"time", map_to:"auto"},
        {name:"resource", height:23, type:"template", default_value:"Resource Label", map_to:"resource_label"},
        {name:"resource_type", height:23, type:"template", default_value:"Resource type", map_to:"resource_type"},
        {name:"resource_size", height:23, type:"template", default_value:"Resource size", map_to:"resource_size"},
        {name:"configuration", height:23, type:"select", options:config ,  map_to:"config_id" },
        {name:"quality", height:23, type:"template",  default_value:1, type:"textarea",  map_to:"quality" },
        {name:"note", height:70, map_to:"note", type:"textarea" ,},
        {name:"price_estimated", height:23, type:"template", map_to:"price_estimated" },

      ];
    return true;
  });
    
  scheduler.attachEvent("onEventSave",function(eid,data){
      var ev = scheduler.getEvent(eid);
      ev.readonly = true;
      var item = {
        event_id: ev.id, 
        resource_id: ev.resource_id,
        start_date: moment(data.start_date).format("YYYY-M-D HH:mm"),
        end_date: moment(data.end_date).format("YYYY-M-D HH:mm"),
        label: data.resource_label,
        text: ev.text,
        price: 100,
      };
      basket[ev.id] = item;
      updateBasket(item);
      return true;
   });

   scheduler.init('scheduler_here',new Date,"timeline");
   var slotsURL = CRM.url('civicrm/booking/ajax/slots');
   scheduler.setLoadMode("day");
   scheduler.load(slotsURL, "json");

  if (cj.trim(cj("#resources").val())) {
      var slots = [];
      var resources = JSON.parse(cj.trim(cj("#resources").val()));
     _.each(resources, function (item, key){ 
        basket[key] = item;
        slots.push(item);
        updateBasket(item);
     });
     scheduler.parse(JSON.stringify(slots),"json");
  }
});

function show_minical(){
  if (scheduler.isCalendarVisible()){
      scheduler.destroyCalendar();
  }else{
      scheduler.renderCalendar({
        position:"dhx_minical_icon",
        date:scheduler._date,
        navigation:true,
        handler:function(date,calendar){
          scheduler.setCurrentView(date);
          scheduler.destroyCalendar()
        }
      });
  }
}

function updateBasket(item){
  subTotal += item.price;
  if(subTotal > 0){ 
    cj('#resourceBasket').show();
    var template = _.template(cj('#selectedResourceRowTemplate').html());
    cj('#resourceBasket > tbody:last').append(template({data: item}));
    cj("#resources").val(JSON.stringify(basket)); //ADD JSON object to basket
    cj('#subTotal').html(subTotal);
  }else{
    cj('#resourceBasket').hide();
  }
}

function removeFromBasket(eventId){
  scheduler.deleteEvent(eventId);
  cj('tr[data-eid=' + eventId + ']').remove();
  subTotal -= 100; //FIX ME, get event price
  cj('#subTotal').html(subTotal);
  delete basket[eventId]; 
  cj("#resources").val(JSON.stringify(basket));
  if(subTotal == 0){
    cj('#resourceBasket').hide();
  }
}
</script>
{/literal}

