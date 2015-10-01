<?php use_stylesheet('filters-record') ?>
<?php include_stylesheets_for_form($filters) ?>
<?php include_javascripts_for_form($filters) ?>


<div id="sf_admin_filter" data-url="<?php echo url_for($sf_context->getModuleName().'/filters', true) ?>">
  <script type="text/javascript"><!--
    $(document).ready(function(){
      $('.tdp-top-filters').hide();
      $.ajax({
        method: 'get',
        url: $('#sf_admin_filter').attr('data-url'),
        success: function(data){
          var tmp = $('<div></div>');
          tmp.get(0).innerHTML = data;
          var scripts = $(tmp).find('script[type="text/javascript"]');
          
          $('.tdp-top-filters').fadeIn();
          data = $.parseHTML(data);
          $('#sf_admin_filter').replaceWith($(data).filter('#sf_admin_filter'));
          LI.tdp_filters();
          LI.filters_record_init();
          LI.filters_executes_ajax_js(scripts);
        }
      });
    });
    
    LI.filters_executes_ajax_js = function(scripts){
      $(scripts).each(function(){
        if ( !$.trim($(this).html()) )
          return;
        eval($(this).html()
          .replace(/jQuery\(document\).ready\(function\(\)\s*{([\s\S]*)}\);/gm, '$1')
          .replace(/\$\(document\).ready\(function\(\)\s*{([\s\S]*)}\);/gm, '$1')
          .replace(/<!--([\s\S]*)-->/gm, '$1')
          .replace(/^\s*var\s+/g, ''));
      });
    }
  --></script>
</div>

