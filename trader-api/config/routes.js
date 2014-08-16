'use strict';
// Draw routes.  Locomotive's router provides expressive syntax for drawing
// routes, including support for resourceful routes, namespaces, and nesting.
// MVC routes can be mapped to controllers using convenient
// `controller#action` shorthand.  Standard middleware in the form of
// `function(req, res, next)` is also fully supported.  Consult the Locomotive
// Guide on [routing](http://locomotivejs.org/guide/routing.html) for additional
// information.
module.exports = function routes() {
  this.root('index#main');
  this.resources('locations', function () {
    this.resources('connections');
    this.resources('commodities', function() {
      this.get('traderoutes','traderoutes#show');
    });
    this.get('traderoutes','traderoutes#show');

  });
  this.post('locations/search','search#show');

  this.resources('commoditygroups', function() {
    this.resources('commoditytypes');
  });
  this.resources('commoditytypes');

  this.get('traderoutes','traderoutes#show');

};
