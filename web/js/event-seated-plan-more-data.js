// the global var that can be used everywhere as a "root"
if ( LI == undefined )
  var LI = {};

LI.seatedPlanMoreDataInitialization = function(url, show, root)
{
  if ( url == undefined )
    return false;
  
  if ( root == undefined )
  {
    console.error('define $("body") as root');
    root = $('body');
  }
  
  $('.picture.seated-plan .more-data:not(.group, .transaction)').remove();
  if ( !show )
    return;
  
  var container = $('<div></div>').addClass('more-data').appendTo($(root).find('.picture.seated-plan'));
  $('#transition').show();
  $.get(url, function(data){
    var color = Math.floor(Math.random()*255)+','+Math.floor(Math.random()*255)+','+Math.floor(Math.random()*255);
    $.each(data, function(key, obj){
      var elt = $('<div></div>')
        .attr('data-seat-id', obj.seat_id)
        .click(function(event){
          $(this).closest('.seated-plan.picture').find('.seat-'+$(this).attr('data-seat-id')+'.txt').trigger(event);
        })
        .css('left', obj.position[0])
        .css('top',  obj.position[1])
        .width(obj.width)
        .appendTo(container);
      ;
      if ( obj.seat_class )
        elt.addClass('seat-extra-'+obj.seat_class);
      
      switch(obj.type) {
      case 'controls':
        $(root).find('.picture.seated-plan > .seat').remove();
      break;
        
      case 'shortname':
        elt
          .addClass('shortname')
          .addClass('obj-'+obj.slug)
          .prop('title', obj.seat_name+' → '+obj.fullname+' #'+obj.transaction_id)
          .text(obj.shortname)
          .addClass('obj-'+obj.slug)
          .attr('data-contact-id', obj.id)
          .each(function(){
            $(this).width($(this).width()+parseInt($(this).css('padding-left'),10)*2);
            $(this).css('padding','0');
          });
        ;
      break;
      
      case 'rank':
        elt
          .addClass('rank')
          .prop('title', obj.seat_name+' → '+obj.rank)
          .text(obj.rank)
          .addClass('rank-'+obj.rank)
          .attr('data-rank', obj.rank)
        ;
      break;
      
      case 'link':
        elt
          .addClass('link')
          .prop('title', obj.names[0]+' - '+obj.names[1])
          .addClass('link-seat-'+obj.ids[0]).addClass('link-seat-'+obj.ids[1])
          .attr('data-link-a', obj.ids[0]).attr('data-link-b', obj.ids[1])
          .css('transform', 'rotate('+obj.angle+'deg)')
          .css('left', obj.position[0][0])
          .css('top',  obj.position[0][1])
          .css('width', obj.length)
          .mouseup(function(event){
            event.stopPropagation();
            return false;
          })
          .mousedown(function(event){
            event.stopPropagation();
            return false;
          })
          .dblclick(function(){
            // DELETE A LINK USING THE GUI/WYSIWYG
            $('#sf_fieldset_neighbors [name="auto_links[exceptions_to_remove]"]').val(
              'eve-ids-'+$(this).attr('data-link-a')+
              '--'+
              $(this).attr('data-link-b')
            );
            $('#sf_fieldset_neighbors [name="auto_links[exceptions_to_remove_submit]"]').attr('data-no-msg', 'no-msg');
            $('#sf_fieldset_neighbors [name="auto_links[exceptions_to_remove_submit]"]').click();
            $(this).remove();
            $('#sf_fieldset_neighbors [name="auto_links[exceptions_to_remove]"]').val('');
          })
        ;
      break;
      
      case 'debt':
        elt
          .addClass('debt')
          .prop('title', obj['debt-txt']+' / '+obj.seat_name+' #'+obj.transaction_id)
          .text(obj.debt != 0 ? obj['debt-txt'] : '')
          .height(elt.width())
          .addClass('debt-'+(obj.debt == 0 ? 'ok' : (obj.debt > 0 ? 'too-much' : 'debt')))
        ;
        if ( obj.seat_class )
          elt.addClass('seat-extra-'+obj.seat_class);
      break;
      
      case 'group':
        elt.closest('.more-data').addClass(obj.type);
        elt
          .addClass('group')
          .height(elt.width())
          .prop('title', obj.group_name)
          .css('background-color', 'rgb('+color+',0.5)')
        ;
        elt.closest('.seated-plan-parent').find('.seated-plan-actions .groups [name=group_id] [value='+obj.group_id+']')
          .css('background-color', 'rgba('+color+',0.5)');
        if ( obj.seat_class )
          elt.addClass('seat-extra-'+obj.seat_class);
      break;
      
      case 'transaction':
        elt.closest('.more-data').addClass(obj.type);
        elt
          .addClass('transaction')
          .height(elt.width())
          .prop('title', obj['transaction-txt'])
          .css('background-color', 'rgb('+color+',0.5)')
        ;
        elt.closest('.seated-plan-parent').find('.seated-plan-actions .transactions [name=transaction_id] [value='+obj.transaction_id+']')
          .css('background-color', 'rgba('+color+',0.5)');
        if ( obj.seat_class )
          elt.addClass('seat-extra-'+obj.seat_class);
      break;
      }
    });
    
    $('#transition .close').click();
  });
}

LI.seatedPlanInitializationFunctions.push(function(root){
  $(root).find('.seat.txt').click(function(e){
    var url = $(this).closest('.seated-plan-parent').find('.seated-plan-actions .transaction').prop('href');
    if ( !url )
      return;
    
    if ( $(this).attr('data-ticket-id') )
      url += '?ticket_id='+$(this).attr('data-ticket-id');
    else if ( $(this).closest('[data-gauge-id]').attr('data-gauge-id') )
      url += '?seat_id='+$(this).attr('data-id')+'&gauge_id='+$(this).closest('[data-gauge-id]').attr('data-gauge-id');
    else
      url += '?seat_id='+$(this).attr('data-id')+'&manifestation_id='+$(this).closest('[data-manifestation-id]').attr('data-manifestation-id');
    
    if ( e.which == 2 || e.ctrlKey || e.metaKey )
      window.open(url);
    else
      window.location = url;
  }).mousedown(function(e){
    if ( e.which == 2 )
      $(this).click(e);
  });
});
