<div id="sf_admin_filter" data-url="<?php echo url_for($sf_context->getModuleName().'/filters', true) ?>">
  <script type="text/javascript"><!--
    $(document).ready(function(){
      $('.tdp-top-filters').hide();
      $.ajax({
        method: 'get',
        url: $('#sf_admin_filter').attr('data-url'),
        success: function(data){
          $('.tdp-top-filters').fadeIn();
          data = $.parseHTML(data);
          $('#sf_admin_filter').replaceWith($(data).filter('#sf_admin_filter'));
          LI.tdp_filters();
        }
      });
    });
  --></script>
</div>

