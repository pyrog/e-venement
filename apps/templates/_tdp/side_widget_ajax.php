<div id="tdp-side-bar" data-url="<?php echo url_for($sf_context->getModuleName().'/sideBar', true) ?>">
  <script type="text/javascript"><!--
    $(document).ready(function(){
      $.ajax({
        method: 'get',
        url: $('#tdp-side-bar').attr('data-url'),
        success: function(data){
          data = $.parseHTML(data);
          $('#tdp-side-bar').replaceWith($(data).filter('#tdp-side-bar').hide().fadeIn());
          LI.tdp_side_bar();
        }
      });
    });
  --></script>
</div>
