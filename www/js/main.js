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

function info_toggle (projectName){
  var elemSelector = projectName + '_info';
  var elem = document.getElementById(elemSelector);
  var toggle = jQuery(elem).toggle();
  var nameToggle = document.getElementById(elemSelector + '_toggle');

  if (toggle.css('display') != 'block'){
    jQuery(nameToggle).find('img').attr('src','images/icon/plus.gif');
    console.log( jQuery.cookie('project_detail[' + projectName + ']', null) );
  }else{
    jQuery(nameToggle).find('img').attr('src','images/icon/minus.gif');
    console.log( jQuery.cookie('project_detail[' + projectName + ']', '1', { expires: 30 }) );
  }
}

