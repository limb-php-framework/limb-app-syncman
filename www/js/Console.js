var ConsoleInsertion = Class.create();
ConsoleInsertion.prototype = Object.extend(new Abstract.Insertion('beforeEnd'), {
  initializeRange: function()
  {
    this.range.selectNodeContents(this.element);
    this.range.collapse(this.element);

    this.processed_content = '';
    this.content = this._processContent(this.content); 
  },

  insertContent: function(fragments)
  {
    fragments.each((function(fragment) {
      this.element.appendChild(fragment);
    }).bind(this));

    this.element.scrollTop = this.element.scrollHeight;
  },

  _processContent: function(content)
  {
    var lines = content.split("\n");
    lines.each(this._processLine.bind(this));
    return this.processed_content;
  },

  _processLine: function(line)
  {
    line = line.replace(/__(command_)?done__/g, '');
    this.processed_content += this._highlight(line);
  },

  _highlight: function(line)
  {
    var result = '';
    var line_parts = line.match(/^([a-z]*@?[a-z]*)\s*(\$?)(.*)/);
      
    if(!line_parts[0]) 
      return line;
    
    if(line_parts[2])
    {
      result += '<span class="color_lightgreen">' + line_parts[1] +  '</span>&nbsp;'; 
      result += '<span class="color_lightblue">$</span>&nbsp;';
    }
    else
      result += line_parts[1];
      
    result += line_parts[3] + '<br>';
    return result;
  }
});

ConsoleUpdater = Class.create();
ConsoleUpdater.prototype = Object.extend(Ajax.PeriodicalUpdater.prototype, {
  initialize: function(container, url, options) 
  {
    this.setOptions(options);
    this.onComplete = this.options.onComplete;
    this.onCommandPerformed = this.options.onCommandPerformed;

    this.frequency = (this.options.frequency || 2);

    this.updater = {};
    this.container = container;
    this.url = url;

    this.received = 0;

    this.start();
  }, 

  updateComplete: function(request) 
  {
    if(request.responseText.match(/__done__/))
    {
      this.stop();
      return;
    }
    
    if(request.responseText.match(/__command_done__/))
      (this.onCommandPerformed || Prototype.EmptyFunction).apply(this, arguments);

    this.received += request.responseText.length;
    
    this.timer = setTimeout(this.onTimerEvent.bind(this),
      this.decay * this.frequency * 1000);
  },

  onTimerEvent: function() 
  {
    var url = this.url;
    if(this.received)
      url = Limb.Http.addUrlQueryItem(url, 'offset', this.received);

    this.updater = new Ajax.Updater(this.container, url, this.options);
  },

  stop: function() 
  {
    this.updater.options.onComplete = undefined;
    clearTimeout(this.timer);
    (this.onComplete || Prototype.emptyFunction).apply(this, arguments);
  } 
}); 

var Console = Class.create();

Console.prototype = {
  initialize: function(container, url, params)
  {
    var updater_params = {
      'frequency': 3,
      'insertion': ConsoleInsertion,
      'method': 'get'
    }

    if(typeof(params['onReady']) == 'function')
      updater_params['onComplete'] = params['onReady'];

    if(typeof(params['onCommandPerformed']) == 'function')
      updater_params['onCommandPerformed'] = params['onCommandPerformed']; 
    this.updater = new ConsoleUpdater(container, url, updater_params);
  },

  freeze: function()
  {
    this.updater.stop();
  }
}
