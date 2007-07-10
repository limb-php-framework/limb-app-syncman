if(typeof(Limb) == 'undefined') var Limb = {};
if(typeof(Limb.Http) == 'undefined') Limb.Http = {};

Limb.Http.getQueryItem = function (page_href, item_name)
{
  arr = Limb.Http.getQueryItems(page_href);

  if(arr[item_name])
    return arr[item_name];
  else
    return null;
}

Limb.Http.buildQuery = function (items)
{
  query = '';
  $H(items).each(function (pair) {
    query += pair.key + '=' + pair.value + '&';
  });
  
  return query;
}

Limb.Http.getQueryItems = function (uri)
{
  query_items = new Array();

  arr = uri.split('?');
  if(!arr[1])
    return query_items;

  query = arr[1];

  arr = query.split('&');

  arr.each(function (value, index) {
    if(typeof(arr[index]) == 'string')
    {
      key_value = arr[index].split('=');
      if(!key_value[1])
        return;

      query_items[key_value[0]] = key_value[1];
    }
  });

  return query_items;
}

Limb.Http.addUrlQueryItem = function (uri, parameter, val)
{
  uri_pieces = uri.split('?');

  items = Limb.Http.getQueryItems(uri);
  items[parameter] = val;
  
  return uri_pieces[0] + '?' + Limb.Http.buildQuery(items);
} 
