var basket = {};
var subTotal = 0;
var configurations = [];

(function(cj) {
    cj.fn.bookingscheduler = function(settings) {

      scheduler.locale.labels.timeline_tab = "Timeline";
      scheduler.locale.labels.section_resource = "Resource";
      scheduler.locale.labels.section_desc = "Description";
      scheduler.locale.labels.section_configuration = "Configuration";
      scheduler.locale.labels.section_quality = "Quality";
      scheduler.locale.labels.section_note = "Note";
      scheduler.locale.labels.section_price_estimated = "Estimated Price";

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
        var rid = ev.resource_id;
        var data = [];
        params = {
          entity: 'BookingResource',
          action: 'get',
          json: JSON.stringify({
            id: rid,
            sequential: 1,
            'api.booking_resource_config_set.get': {
              id: '$value.set_id',
              'api.booking_resource_config_option.get': {
                set_id: '$value.id'
              }
            }
          }),
        };
        //HACKED, CRM.api does not support ajax option so set async was not possible
        var ajaxURL = 'civicrm/ajax/rest';
        cj.ajax({
          url: ajaxURL.indexOf('http') === 0 ? ajaxURL : CRM.url(ajaxURL),
          dataType: 'json',
          data: params,
          async: false,
          type: 'GET',
          success: function(result) {
            data = result;
          }
        });
        var resource = data['values']['0'];
        var config = [];
        var options = data['values']['0']['api.booking_resource_config_set.get']['values']['0']['api.booking_resource_config_option.get']['values'];
        _.each(options, function (item, key){
          var label = [item.label, " - ", item.price].join("");
          config.push({key: item.id, label: label});
          configurations.push({key: item.id, item: item});
        });

        scheduler.config.lightbox.sections=[
          {name:"time", height:72, type:"time", map_to:"auto"},
          {name:"resource", height:23, type:"template", default_value:resource.label, map_to:"resource_label"},
          {namn:"desc", height:23, type:"template", default_value:resource.description, map_to:"resource_description"},
          {name:"configuration", height:23, type:"select", options:config ,  map_to:"config_id" },
          {name:"quality", height:23, type:"template",  default_value:1, type:"textarea",  map_to:"quality" },
          {name:"note", height:70, map_to:"note", type:"textarea" ,},
          {name:"price_estimated", height:23, type:"template", map_to:"price_estimated" },

        ];
        return true;
    });

    scheduler.attachEvent("onEventSave",function(eid,data){
        var ev = scheduler.getEvent(eid);
        console.log(ev);
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
  };
})(cj);

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
    console.log(item);
    if(subTotal > 0){
      cj('#basket-table').show();
      var template = _.template(cj('#selected-resource-row-tpl').html());
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

cj(function($) {
  function loadEvents(){
    if ($.trim($("#resources").val())) {
        var slots = [];
        var resources = JSON.parse($.trim($("#resources").val()));
        _.each(resources, function (item, key){
          basket[key] = item;
          item.readonly = true;
          slots.push(item);
          updateBasket(item);
        });
        scheduler.parse(JSON.stringify(slots),"json");
    }
  }
  $(document).ready(loadEvents);
});

