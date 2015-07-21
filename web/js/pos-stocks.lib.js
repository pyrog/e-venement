if ( LI == undefined )
  var LI = {};

LI.posRenderStocks = function(data, jObj)
{
  if ( typeof data.declinations != 'object' )
    return;
  
  var fdata = [[], [], []];
  var ticks = [];
  var cpt = 0;
  $.each(data.declinations, function(id, stocks){
    ticks[cpt] = stocks.name;
    if ( stocks.current <= stocks.critical )
    {
      fdata[0][cpt] = stocks.current;
      fdata[1][cpt] = 0;
      fdata[2][cpt] = 0;
    }
    else if ( stocks.current > stocks.critical && stocks.current <= stocks.perfect )
    {
      fdata[0][cpt] = 0;
      fdata[1][cpt] = stocks.current;
      fdata[2][cpt] = 0;
    }
    else
    {
      fdata[0][cpt] = 0;
      fdata[1][cpt] = 0;
      fdata[2][cpt] = stocks.current;
    }
    cpt++;
  });
  
  $.jqplot(
    jObj.prop('id'), fdata, {
      series: [
        { label: data.texts.critical },
        { label: data.texts.correct },
        { label: data.texts.perfect }
      ],
      stackSeries: true,
      seriesColors: [
        'rgba(255,0,0,0.7)',
        'rgba(255,165,0,0.7)',
        'rgba(0,128,0,0.7)'
      ],
      seriesDefaults: {
        renderer: $.jqplot.BarRenderer,
        rendererOptions: { barMargin: 30 },
        pointLabels: {
          stackedValue: true,
          location: 's',
          show: true
        }
      },
      legend: {
        show: true,
        location: 'e',
        placement: 'outside'
      },
      axes: {
        xaxis: {
          ticks: ticks,
          renderer: $.jqplot.CategoryAxisRenderer,
          autoscale: true,
          
        }
      },
      highlighter: {
        tooltipAxes: 'y',
        sizeAdjust: 2,
        show: true
      },
      captureRightClick: true
    }
  );
}
