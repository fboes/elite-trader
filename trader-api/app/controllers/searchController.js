'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller;

var searchController = new Controller();


searchController.show = function() {
  var self = this;
  
  var location_id = this.params('location_id');
  var name = this.params('name');

  return self.res.json( { error : "Not yet implemented! "+location_id+":"+name} );
};

module.exports = searchController;
