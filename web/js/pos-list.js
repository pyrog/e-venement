$(document).ready(function(){
  $('.sf_admin_list_td_list_stocks .data').each(function(){
    LI.posRenderStocks(
      JSON.parse($(this).text()),
      $(this).closest('.sf_admin_list_td_list_stocks').find('.jqplot')
    );
  });
});
