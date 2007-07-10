TestSourceFetcher = {
  get: function(url)
  {
    return this._src[url];
  },

  getOnce: function(url)
  {
    return this.get(url);
  },

  cleanUp: function()
  {
    this._src = [];
  },

  set: function(url, src)
  {
    if(!this._src)
      this._src = [];

    this._src[url] = src;
  }
}
