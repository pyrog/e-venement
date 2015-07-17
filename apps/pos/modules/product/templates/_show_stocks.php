<?php use_javascript('/js/jqplot/jquery.jqplot.js') ?>
<?php use_stylesheet('/js/jqplot/jquery.jqplot.css') ?>
<?php use_javascript('/js/jqplot/jqplot.axisLabelRenderer.js') ?>
<?php use_javascript('/js/jqplot/jqplot.axisTickRenderer.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.categoryAxisRenderer.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.pointLabels.js') ?>
<?php use_javascript('/js/jqplot/plugins/jqplot.barRenderer.js') ?>

<div id="stocks_chart" style="width: 450px;">
</div>
<script type="text/javascript"><!--
  if ( LI == undefined )
    var LI = {};
  
  var cpt = 0;
  var ticks = [];
  var series = [[], [], []];
  $('.sf_admin_form_field_declinations .widget > table > tbody > tr').each(function(){
    var stocks = {
      critical: parseInt($(this).find('[name="product[declinations]['+cpt+'][stock_critical]"]').val()),
      current:  parseInt($(this).find('[name="product[declinations]['+cpt+'][stock]"]').val()),
      perfect:  parseInt($(this).find('[name="product[declinations]['+cpt+'][stock_perfect]"]').val())
    };
    
    if ( stocks.current <= stocks.critical )
    {
      series[0][cpt] = stocks.current;
      series[1][cpt] = 0;
      series[2][cpt] = 0;
    }
    else if ( stocks.current > stocks.critical && stocks.current <= stocks.perfect )
    {
      series[0][cpt] = 0;
      series[1][cpt] = stocks.current;
      series[2][cpt] = 0;
    }
    else
    {
      series[0][cpt] = 0;
      series[1][cpt] = 0;
      series[2][cpt] = stocks.current;
    }
    ticks[cpt] = $(this).find('[name="product[declinations]['+cpt+'][code]"]').val();
    cpt++;
  });
  
  $.jqplot(
    'stocks_chart',
    series,
    {
      series: [
        { label: 'Critical' },
        { label: 'Normal' },
        { label: 'Perfect' }
      ],
      stackSeries: true,
      seriesColors: [ 'rgba(255,0,0,0.7)', 'rgba(255,165,0,0.7)', 'rgba(0,128,0,0.7)' ],
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
          renderer: $.jqplot.CategoryAxisRenderer
        },
        yaxis: {
        }
      },
      captureRightClick: true
    }
  );
--></script>
