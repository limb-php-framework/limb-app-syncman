Limb.namespace('Limb.browser');

var agt = navigator.userAgent.toLowerCase();
Limb.browser.is_ie = (agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1);
Limb.browser.is_gecko = navigator.product == "Gecko";
Limb.browser.is_opera  = (agt.indexOf("opera") != -1);
Limb.browser.is_mac    = (agt.indexOf("mac") != -1);
Limb.browser.is_mac_ie = (Limb.browser.is_ie && Limb.browser.is_mac);
Limb.browser.is_win_ie = (Limb.browser.is_ie && !Limb.browser.is_mac);

Limb.browser.detectFlash = function(requiredVersion)
{
  var flashVersion = 0;

  if (!navigator.plugins)
    return false;

  if(Limb.browser.is_win_ie)
  {
    var flashPresent = false;

    for(var version = requiredVersion; version<10; version++)
    {
      try
      {
        flashPresent = flashPresent || new ActiveXObject('ShockwaveFlash.ShockwaveFlash.' + version);
      }
      catch(e) {}
    }

    return flashPresent;
  }

  if (navigator.plugins["Shockwave Flash 2.0"]
      || navigator.plugins["Shockwave Flash"])
  {

    var isVersion2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
    var flashDescription = navigator.plugins["Shockwave Flash" + isVersion2].description;

    var flashVersion = parseInt(flashDescription.substring(16));
  }

  if(navigator.userAgent.indexOf("WebTV") != -1) actualVersion = 4;

  if (flashVersion < requiredVersion)
    return false;

  return true;
}

