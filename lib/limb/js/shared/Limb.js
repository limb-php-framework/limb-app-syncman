if(Limb == undefined) var Limb = {};

String.prototype.trim = function()
{
  var r=/^\s+|\s+$/;
  return this.replace(r,'');
}

Limb.namespace = function(name)
{
  parts = name.split('.');
  parts_string = '';
  for(var i=0; i<parts.length; i++)
  {
    if(!parts[i]) continue;
    parts_string += ('.' + parts[i]);
    exec = 'if(window' + parts_string + ' == undefined) window' + parts_string + ' = {};';
    eval(exec);
  }
}

Limb.require = function(file)
{
  document.write('<script type="text/javascript" src="'+file+'"></script>');
}

Limb.isset = function(obj)
{
  return typeof(obj) != undefined && obj != null;
}


Limb.inspectError = function(obj)
{
  var info = [];

  if(typeof obj=="string" || typeof obj=="number")
    return obj;
  else
  {
    for(property in obj)
      if(typeof obj[property] != "function")
        info.push(property + ' => ' +
          (typeof obj[property] == "string" ? '"' + obj[property] + '"' : obj[property]));
  }

  return ("'" + obj + "' #" + typeof obj +
    ": {" + info.join(", ") + "}");
}

Limb.namespace('Class');

Class.__new__ = function () {
    // private token, naked unique object
    var __clone__ = {};
    // the incrementing counter for instances created
    var instanceCounter = 0;
    // This is the representation of class objects
    var classToString = function () {
        return "[Class " + this.NAME + "]";
    };
    // Representation of instance objects
    var instanceToString = function () {
        return "[" + this.__class__.NAME + " #" + this.__id__ + "]";
    };
    var forwardToRepr = function () {
        return this.__repr__();
    };
    var proxyFunction = function (func) {
        var callFunc = func.__orig__;
        if (typeof(callFunc) == 'undefined') {
            callFunc = func;
        }
        var newFunc = function () {
            return callFunc.apply(this, arguments);
        }
        for (var k in func) {
            newFunc[k] = func[k];
        }
        newFunc.__orig__ = callFunc;
        return newFunc;
    };

    var getNextMethod = function (self) {
        var next_method = null;
        try {
            return this.__class__.superClass.prototype[this.__name__];
        } catch (e) {
            throw new TypeError("no super method for " + this.NAME);
        }
    }

    var nextMethod = function (self) {
        var args = [];
        for (var i = 1; i < arguments.length; i++) {
            args.push(arguments[i]);
        }
        var next = this.getNextMethod();
        if ( typeof( next ) == 'function' ) {
            next.apply(self, args);
        }
    };

    this.create = function () {
        var body = null;
        var name = "Some Class";
        var superClass = Object;

        if ( arguments.length == 1 ) {
            name = arguments[0];
        }
        else if ( arguments.length == 2 ) {
            if ( typeof arguments[0] == 'string' ) {
                name = arguments[0];
            }
            else {
                superClass = arguments[0];
            }
            body = arguments[1];
        }
        else {
            name = arguments[0];
            superClass = arguments[1];
            body = arguments[2];
        }

        // this is the constructor we're going to return
        var rval = function (arg) {
            // allow for "just call" syntax to create objects
            var o = this;
            if (!(o instanceof rval)) {
                o = new rval(__clone__);
            } else {
                o.__id__ = ++instanceCounter;
            }
            // don't initialize when using the stub method!
            if (arg != __clone__) {
                if (typeof(o.initialize) == 'function') {
                    o.initialize.apply(o, arguments);
                }
            }
            return o;
        };

        rval.NAME = name;
        rval.superClass = superClass;
        rval.toString = forwardToRepr;
        rval.__repr__ = classToString;
        rval.__MochiKit_Class__ = true;

        if ( body ) {
            this.extend( rval, superClass, body );
        }

        return rval;
    };

    this.extend = function ( rval, superClass, body ) {

        var proto = null;
        if (superClass.__MochiKit_Class__) {
            proto = new superClass(__clone__);
        } else {
            proto = new superClass();
        }

        if (typeof(proto.toString) == 'undefined' || (proto.toString == Object.prototype.toString)) {
            proto.toString = instanceToString;
        }
        if (typeof(proto.__repr__) == 'undefined') {
            proto.__repr__ = instanceToString;
        }
        if (proto.toString == Object.prototype.toString) {
            proto.toString = forwardToRepr;
        }
        if (typeof(body) != 'undefined' && body != null) {
            for (var k in body) {
                var o = body[k];
                if (typeof(o) == 'function' && typeof(o.__MochiKit_Class__) == 'undefined') {
                    if (typeof(o.__class__) != 'undefined') {
                        if (o.__class__ != rval) {
                            continue;
                        }
                        o = proxyFunction(o);
                    }
                    o.__class__ = rval;
                    o.__name__ = k;
                    o.NAME = rval.NAME + '.' + k;
                    o.nextMethod = nextMethod;
                    o.getNextMethod = getNextMethod;
                }
                proto[k] = o;
            }
        }
        proto.__id__ = ++instanceCounter;
        proto.__class__ = rval;

        proto.__super__ = function ( methname ) {
            if ( typeof( this[methname] ) != 'function' ) return;
            var args = [];
            for ( var i = 1; i < arguments.length; i++ )
                args.push( arguments[i] );

            this[methname].nextMethod( this, args );
        };

        proto.__super__.__class__ = superClass;
        proto.__super__.__name__ = '__super__';
        proto.__super__.NAME = rval.NAME + '.__super__';
        proto.__super__.nextMethod = nextMethod;
        proto.__super__.getNextMethod = getNextMethod;

        rval.prototype = proto;
    };

    this.subclass = function () {
        var body = {};
        var name = "Some Class";
        var superClass = Object;

        if ( arguments.length == 1 ) {
            body = arguments[0];
        }
        else if ( arguments.length == 2 ) {
            superClass = arguments[0];
            body = arguments[1];
        }
        else {
            name = arguments[0];
            superClass = arguments[1];
            body = arguments[2];
        }

        var rval = this.create( name, superClass, body );
        this.extend( rval, superClass, body );

        return rval;
    };
    this.subclass.NAME = this.NAME + "." + "subclass";
};

Class.__new__();
