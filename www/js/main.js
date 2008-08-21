function on_load(){
  jQuery(document).ready(function(){
  
    jQuery("tr.list:nth-child(odd)").addClass("odd");  
    jQuery("tr.list").bind("mouseover", function(){
            jQuery(this).find("td").css('backgroundColor','#e2e7ec');
          });
    jQuery("tr.list").bind("mouseout", function(){
          if (jQuery(this).attr('class')== 'list odd') 
            jQuery(this).find("td").css('backgroundColor','#F9F9F9');
          else
            jQuery(this).find("td").css('backgroundColor','#fff');
          });
  });
}

on_load();

function category_toggle (elem_selector, display){
  var name_toggle = document.getElementById(elem_selector + '_toggle');
  if (jQuery(name_toggle).find('img').attr('src') != 'images/icon/open.gif')
      jQuery(name_toggle).find('img').attr('src','images/icon/open.gif');
  else 
      jQuery(name_toggle).find('img').attr('src','images/icon/close.gif');
}

function info_toggle (elem_selector){
  var elem = document.getElementById(elem_selector);
  var toggle = jQuery(elem).toggle();    
  var name_toggle = document.getElementById(elem_selector + '_toggle');
  if (toggle.css('display') != 'block')
     { jQuery(name_toggle).find('img').attr('src','images/icon/plus.gif');}
  else 
      {jQuery(name_toggle).find('img').attr('src','images/icon/minus.gif');}
      
}

