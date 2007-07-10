Limb.namespace('Limb.events');

Limb.events.add_event = function (control, type, fn, use_capture)
{
 if (control.addEventListener)
 {
   control.addEventListener(type, fn, use_capture);
   return true;
 }
 else if (control.attachEvent)
 {
   var r = control.attachEvent("on" + type, fn);
   return r;
  }
}
