function getDetailForCategory(category) {
  aOnClick = jQuery('a#' + category + '_toggle').attr('onclick');
  aHref = jQuery('a#' + category + '_toggle').attr('href');

  jQuery('a#' + category + '_toggle').attr('onclick', 'return false;');
  jQuery('a#' + category + '_toggle').attr('href', '#');
  jQuery('div#' + category).html("Loading...");
  
  jQuery.get(aHref + '&js=1',
    function (data) { 
      //jQuery('div#' + category).html(data);
      document.getElementById(category).innerHTML = data;
      on_load();
    });
  jQuery('a#' + category + '_toggle').attr('onclick', aOnClick);
  jQuery('a#' + category + '_toggle').attr('href', aHref);
}