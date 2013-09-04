var basket = {};
var subTotal = 0.00;

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


cj(function($) {

  scheduler.locale.labels.timeline_tab = "Timeline";
  scheduler.config.show_loading = true;
  scheduler.config.full_day = true;
  scheduler.config.details_on_create=true;
  scheduler.config.details_on_dblclick=false;
  scheduler.config.collision_limit = 1; //allows creating 1 events per time slot
  scheduler.config.xml_date="%Y-%m-%d %H:%i";

  scheduler.init("resource_scheduler", new Date() ,"timeline");
  scheduler.setLoadMode("day");
  scheduler.load(CRM.url('civicrm/booking/ajax/slots'),"json");

  scheduler.showLightbox = function(id) {
    var ev = scheduler.getEvent(id);

    scheduler.startLightbox(id,null);
    scheduler.hideCover();
    $("#crm-booking-new-slot").dialog({
        title: ts('Add resource to basket'),
        modal: true,
        minWidth: 600,
        open: function() {
          //insert the template so tag <form /> will work for validation
          var template = _.template(cj('#add-resource-template').html());
          $('#crm-booking-new-slot').html(template());
          //TODO: Implement validation and using CiviCRM validation style
          $('#add-resource-form').validate({
                rules: {
                    configuration: {
                      required: true
                    },
                    quantity: {
                      required: true,
                      number: true
                    },
                }
          });

          if(ev.readonly){
            $(".crm-booking-form-add-resource").attr("disabled", true);
            $("#price-estimate").html(ev.price);
            $("#note").val(ev.note);
            $("input[name='quantity']").val(ev.quantity);
            $("#add-resource-btn").hide();
          }else{
            $("#SelectResource :input").attr("disabled", false);
            $("#price-estimate").html('0');
            $("#note").val('');
            $("input[name='quantity']").val('');
            $("#add-resource-btn").show();
          }
          var initStartDate = moment(new Date(ev.start_date));
          var initEndDate = moment(new Date(ev.end_date));
          var startTime = [initStartDate.hours(), ":", initStartDate.minute() <10?'0' + initStartDate.minute() : initStartDate.minute()].join("");
          var endTime = [initEndDate.hours(), ":", initEndDate.minute() <10?'0' + initEndDate.minute() : initEndDate.minute()].join("");
          $("#start-time-select").val(startTime);
          $("#start-day-select").val(initStartDate.format("D"));
          $("#start-month-select").val(initStartDate.months() + 1);
          $("#start-year-select").val(initStartDate.years());
          $("#end-time-select").val(endTime);
          $("#end-day-select").val(initEndDate.format("D"));
          $("#end-month-select").val(initEndDate.months() + 1);
          $("#end-year-select").val(initEndDate.years());

         CRM.api('Resource', 'get', {
              id: ev.resource_id,
              sequential: 1,
              'api.resource_config_set.get': {
                id: '$value.set_id',
                'api.resource_config_option.get': {
                  set_id: '$value.id',
                  'api.option_group.get':{
                    name: 'booking_size_unit',
                  },
                  'api.option_value.get':{
                    value: '$value.unit_id',
                    option_group_id: '$value.api.option_group.get.value'
                  }
                }
              }
            },
          {
            success: function(data) {
            var resource =  data['values']['0'];
            $("#resource-label").val(resource.label);
            var options = data['values']['0']['api.resource_config_set.get']['values']['0']['api.resource_config_option.get']['values'];
            var optionsTemp = [];
            if(ev.readonly){
              var configId = ev.configuration_id;
               _.each(options, function (item, key){
                if(item.id == configId){
                  item.selected = "selected";
                }else{
                  item.selected = "";
                }
                optionsTemp.push(item);
              });
              options = optionsTemp;
            }
            var template = _.template(cj('#select-config-option-template').html());
            $('#configSelect').html(template({
              options: options,
              first_option:  ["- ", ts('select configuration')," -"].join("")}));
              }
            });

        },
        close: function() {
          scheduler.endLightbox(false, null);
          $(this).dialog('destroy');
        },
    });
  };


 $(document).on("click", 'input[name="select-resource-save"]', function(e){
    e.preventDefault();
    if (!$('#add-resource-form').valid()) {
        return false;
    }
    var ev = scheduler.getEvent(scheduler.getState().lightbox_id);
    var startTime = $("#start-time-select").val().split(":");
    var startDate = new Date($("#start-year-select").val(), $("#start-month-select").val() - 1, $("#start-day-select").val(), startTime[0], startTime[1]);
    var endTime = $("#end-time-select").val().split(":");
    var endDate = new Date($("#end-year-select").val(), $("#end-month-select").val() - 1, $("#end-day-select").val(), endTime[0], endTime[1]);
    ev.text = [$("#resource-label").val(), " - ", ev.id].join("");
    ev.start_date = startDate;
    ev.end_date = endDate;
    ev.price = $("#price-estimate").html();
    ev.quantity = $('input[name="quantity"]').val();
    ev.configuration_id = $('#configSelect').val();
    ev.note = $("note").val();
    ev.readonly = true;
    var item = {
      id: ev.id,
      resource_id: ev.resource_id,
      start_date:  moment(ev.start_date).format("YYYY-M-D HH:mm"),
      end_date: moment(ev.end_date).format("YYYY-M-D HH:mm"),
      label: $("#resource-label").val(),
      text: ev.text,
      configuration_id: ev.configuration_id ,
      quantity: ev.quantity,
      price: ev.price,
      note: ev.note,
    };
    basket[ev.id] = item;
    updateBasket(item);
    scheduler.endLightbox(true,null);
    $("#crm-booking-new-slot").dialog('close');
  });

 $(document).on("click", ".remove-from-basket-btn", function(e){
    e.preventDefault();
    var eid = $(this).data('eid');
    var ev = scheduler.getEvent(eid);
    subTotal -=  ev.price;
    delete basket[eid];
    $('tr[data-eid=' + eid + ']').remove();
    $('#subTotal').html(subTotal);
    $("#resources").val(JSON.stringify(basket));
    if(subTotal == 0 || isNaN(subTotal)){
      $('#basket-region').hide();
    }
    scheduler.deleteEvent(eid);
    CRM.alert(ts(''), ts('Resource removed'), 'success');
  });


 $(document).on('click', 'input[name="select-resource-cancel"]', function(e){
    e.preventDefault();
    scheduler.endLightbox(false, null);
    $("#crm-booking-new-slot").dialog('close');
  });

  $(document).on('keypress keyup keydown', 'input[name="quantity"]',  function(e) {
    var price = $("#configSelect").find(':selected').data('price');
    var priceEstimate = price * $(this).val();
    if(!isNaN(priceEstimate)){
      $('#price-estimate').html(priceEstimate);
    }
  });

  $('#configSelect').change(function(e) {
    var val = $(this).val();
    if(val == ""){
      $('input[name="quantity"]').attr("disabled",true);
      $('#price-estimate').html(0);
    }else{
      $('input[name="quantity"]').attr("disabled",false);
    }
  });


  function updateBasket(item){
    subTotal =  parseFloat(subTotal) + parseFloat(item.price);
    if(subTotal > 0){
      var template = _.template(cj('#selected-resource-row-tpl').html());
      $('#basket-table > tbody:last').append(template({data: item}));
      $("#resources").val(JSON.stringify(basket)); //ADD JSON object to basket
      $('#subTotal').html(subTotal);
      $('#basket-region').show();
    }else{
      $('#basket-region').hide();
    }
  }

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

