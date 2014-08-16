'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Location = require('../models/location.js');


var connectionsController = new Controller();

connectionsController.before('*', function( next ) {
  var self = this;

  if ( this.params('location_id') ) {

    var locationid = this.params('location_id');
    var what = { _id : locationid }
    // Does the id contain a :?
    if ( locationid.indexOf(':') != -1 ) {
      // We do not have a real object id here, but a "search"
      var key = locationid.split(':')[0];
      var value = locationid.split(':')[1];
      what = { };
      what[key] = value;
    }

    Location.findOne( what, function( err, location ) {
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
  if ( ! self._location ) {
    return self.res.json( { error : "Location not found!" } );
  }

  // Connection already exists?
  for ( var i = 0; i<self._location.connections.length; ++i ) {
    if ( self._location.connections[i].destination == self.params('destination') ) {
      return self.res.json( { error : "Connection already in list! Maybe you want to update instead?" } );
    }
  }

  // Check if destination exists
  Location.findOne( { _id : self.params('destination') }, function( err, destination ) {
    if (err || ! destination ) {
      self.res.json( { error : "Destination location does not exists!" } );
      return;
    }

    Location.findOneAndUpdate(
      { _id : self.params('location_id') },
      { $push : { connections : { destination : self.params('destination'), distance : self.params('distance') } } },
      function( err, destination ) {
        if (err) {
          self.res.json( { error : "Kaputh!" } );
          return;
        }
        self.res.json( destination );
    });

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
