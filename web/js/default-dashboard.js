if ( LI == undefined )
  var LI = {};

$(document).ready(function(){
  LI.dashboardStats();
  if ( $('#dashboard > .jqplot').length > 0 )
  {
    $('#sf_admin_content .welcome > .ui-widget-content:first')
      .append($('#dashboard'))
      .append('<div class="clear"></div>')
      .find('h3, ul').remove()
    ;
  }
});

LI.dashboardStats = function(){
  $('#dashboard > .jqplot').each(function(){
    var name = $(this).find('.chart').attr('data-series-name');
    var id = $(this).find('.chart').prop('id');
    var title = $(this).find('h2').prop('title') ? $(this).find('h2').prop('title')+': ' : '';
    LI.csvData[name] = [
      [
        title,
        $(this).find('h2').text()
      ],
    ]; 
    
    $.get($(this).find('.chart').attr('data-json-url'), function(json){
      var array = [];
      var series = [];
      
      switch ( name ) {
      case 'debts':
        $.each(json, function(i, data) {
          array.push([data.date, data.outcome - data.income]);
          LI.csvData[name].push([data.date, data.outcome, data.income, data.outcome - data.income]);
        });
        $(this).dblclick(function(){
          $(this).resetZoom();
        });
        break;
      case 'web-origin':
        $.each(json, function(date, value){
          array.push([date, value]);
          LI.csvData[name].push([date, value]);
        });
        $(this).dblclick(function(){
          $(this).resetZoom();
        });
        break;
      case 'geo':
        $.each(json.tickets, function(key, value) {
          array.push([json.translations[key], value]);
          LI.csvData[name].push([json.translations[key], value]);
        });
        break;
      default:
        $.each(json, function(i, data){
          array.push([data.name, data.nb]);
          LI.csvData[name].push([data.name, data.nb]);
        });
      }
      
      switch ( name ) {
      case 'web-origin':
      case 'debts':
        console.error(array);
        $.jqplot(id, [array], {
          seriesDefaults: {
            showMarker: false
          },
          series: [{ label: title }],
          axes: {
            xaxis: {
              renderer: $.jqplot.DateAxisRenderer,
              tickOptions: { formatString:'%d/%m/%Y' }
            },
           yaxis: {
              min: name == 'web-origin' ? 0 : null,
              //tickInterval: 1,
              tickOptions: {
                formatString: '%d'
              }
            }
          },
          highlighter: {
            sizeAdjust: 2,
            show: true
          },
          legend: {
            show: true,
            location: 'e',
            placement: 'outside'
          },
          cursor: {
            show: true,
            showTooltip: false,
            zoom: true
          },
          captureRightClick: true
        });
        break;
      
      default:
        $.jqplot(id, [array], {
          seriesDefaults: {
            rendererOptions: {
              fill: false,
              showDataLabels: true,
              slideMargin: 4,
              lineWidth: 5
            },
            renderer: $.jqplot.PieRenderer
          },
          cursor: {
            showTooltip: false,
            show: true
          },
          legend: {
            show: true,
            location: 'e'
         },
          captureRightClick: true
        });
        break;
      }
    });
  });
}
