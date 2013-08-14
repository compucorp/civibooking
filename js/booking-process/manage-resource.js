
/*
* +--------------------------------------------------------------------+
* | CiviCRM version 4.1                                                |
* +--------------------------------------------------------------------+
* | Copyright CiviCRM LLC (c) 2004-2011                                |
* +--------------------------------------------------------------------+
* | This file is a part of CiviCRM.                                    |
* |                                                                    |
* | CiviCRM is free software; you can copy, modify, and distribute it  |
* | under the terms of the GNU Affero General Public License           |
* | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
* |                                                                    |
* | CiviCRM is distributed in the hope that it will be useful, but     |
* | WITHOUT ANY WARRANTY; without even the implied warranty of         |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
* | See the GNU Affero General Public License for more details.        |
* |                                                                    |
* | You should have received a copy of the GNU Affero General Public   |
* | License and the CiviCRM Licensing Exception along                  |
* | with this program; if not, contact CiviCRM LLC                     |
* | at info[AT]civicrm[DOT]org. If you have questions about the        |
* | GNU Affero General Public License or the licensing of CiviCRM,     |
* | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
* +--------------------------------------------------------------------+
*/ 


var basket = {};
var subTotal = 0;


(function($) { 
    $.fn.bookingscheduler = function(settings) {

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

      scheduler.createTimelineView({
      section_autoheight: false,
        name: "timeline",
        x_unit: "minute",
        x_date: "%H:%i",
        x_step: 30,
        x_size: 24,
        x_start: 16,
        x_length: 48,
        y_unit: settings.elements,
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
          id: ev.id, 
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

    scheduler.init('resource_scheduler', settings.date,"timeline");
    scheduler.setLoadMode(settings.loadMode);
    scheduler.load(settings.url, "json");


    if (cj.trim(cj("#resources").val())) {
      var slots = [];
      var resources = JSON.parse(cj.trim(cj("#resources").val()));
      _.each(resources, function (item, key){ 
        basket[key] = item;
        item.readonly = true;
        slots.push(item);
        updateBasket(item);
      });
      scheduler.parse(JSON.stringify(slots),"json");
    } 
  };
})(jQuery);


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
    cj('#basket-table').show();
    var template = _.template(cj('#selectedResourceRowTemplate').html());
    cj('#basket-table > tbody:last').append(template({data: item}));
    cj("#resources").val(JSON.stringify(basket)); //ADD JSON object to basket
    cj('#subTotal').html(subTotal);
  }else{
    cj('#basket-table').hide();
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
    cj('#basket-table').hide();
  }
}
