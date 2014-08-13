'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Location = require('../models/location.js');


var connectionsController = new Controller();

connectionsController.before('*', function( next ) {
  var self = this;
  if ( this.params('location_id') ) {
    Location.findOne( { _id : this.params('location_id') }, function( err, location ) {
      if ( err ) { return next(err); }
      self._location = location;
      return next();
    });
  }
  else {
    return next();
  }
});

connectionsController.create = function() {
  var self = this;

  // Location available?
  if ( ! this._location ) {
    return self.res.json( { error : "Location not found!" } );
  }

  // Connection already exists?
  for ( var i = 0; i<self._location.connections.length; ++i ) {
    if ( self._location.connections[i].destination == self.params('destination') ) {
      return self.res.json( { error : "Connection already in list! Maybe you want to update instead?" } );
    }
  }

  Location.findOneAndUpdate(
    { _id : this.params('location_id') },
    { $push : { connections : { destination : this.params('destination'), distance : this.params('distance') } } },
    function( err, destination ) {
      if (err) {
        self.res.json( { error : "Kaputh!" } );
        return;
      }
      self.res.json( destination );
  });
};

connectionsController.index = function() {
  var self = this;
  self.res.json( self._location.connections );
};

connectionsController.show = function() {
  var self = this;
  self.res.json( { error : "Not yet implemented!" } );

};

module.exports = connectionsController;
