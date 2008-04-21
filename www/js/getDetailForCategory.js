function getDetailForCategory(category) {
  aOnClick = document.getElementById(category + '_toggle').onclick;
  aHref = document.getElementById(category + '_toggle').href;

  document.getElementById(category + '_toggle').onclick = function onclick(event) {return false;};
  document.getElementById(category + '_toggle').href = '#';
  document.getElementById(category).innerHTML = "Loading..."
  // Create new JsHttpRequest object.
  var req = new JsHttpRequest();
  // Code automatically called on load finishing.
  req.onreadystatechange = function() {
      if (req.readyState == 4) {
          // Write debug information too (output becomes responseText).
          document.getElementById(category).innerHTML = req.responseText;
          document.getElementById(category + '_toggle').onclick = aOnClick;
          document.getElementById(category + '_toggle').href = aHref;
          category_toggle(category);
          on_load();
        }
  }
  // Prepare request object (automatically choose GET or POST).
  req.open('GET', aHref + '&js=1', true);
  // Send data to backend.
  req.send( );
}