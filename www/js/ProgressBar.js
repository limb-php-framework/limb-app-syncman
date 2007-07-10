var ProgressBar = Class.create();

ProgressBar.prototype = {
  initialize: function(container, options)
  {
    if(!options)
      options = [];

    this.container = $(container);
    this.timer = null;
    this.points = [];
    this.points_line = null;
    this.position = 0;
    this.speed = options.speed || 1.0;

    this._createPointsLine();
    this._initPoints(options.points_count || 5, options.spacing || 4);
    this.reset();
    this.start();
  },

  start: function()
  {
    this.timer = setInterval(this.onTimerTick.bind(this), 60 * this.speed);
  },

  reset: function()
  {
    this.position = this.points_line.realWidth * -1;
    this._movePointsLine();
  },

  stop: function()
  {
    clearTimeout(this.timer);
    this.timer = null;
    this.reset();
  },

  onTimerTick: function()
  {
    this.position += this._getStep();
    if(this.position > this.points_line.realWidth + this.points_line.offsetWidth)
      this.reset();

    this._movePointsLine();
  },

  _getStep: function()
  {
    return parseInt((this.container.offsetWidth / this.points_line.realWidth) * 5);
  },

  _createPointsLine: function()
  {
    this.points_line = document.createElement('div');
    this.points_line.style.position = 'relative';
    this.container.appendChild(this.points_line);
  },

  _initPoints: function(points_count, spacing)
  {
    for(var i = 0; i < points_count; i++)
    {
      var point = this._createPoint(i + 1, points_count);

      this.points_line.appendChild(point);
      this.points.push(point);
    }

    this.points_line.realWidth = point.offsetWidth * points_count + spacing * points_count;
  },

  _createPoint: function(point_number, points_count)
  {
    var point = document.createElement('div');
    point.className = 'point';

    var opacity = point_number / points_count;
    point.style.opacity = opacity;
    point.style.filter = 'alpha(opacity=' + opacity + ')';

    return point;
  },

  _movePointsLine: function()
  {
    this.points_line.style.left = this.position + 'px';
  }
}
