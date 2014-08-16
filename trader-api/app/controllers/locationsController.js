'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Location = require('../models/location.js');

var locationsController = new Controller();

locationsController.create = function() {
  var location = new Location( { name : this.param('name') } );
  var self = this;
  location.save( function( err ) {
    if ( err ) {
      return self.res.json( { error : "Location could not be created!", msg:err} );
    }
    return self.res.json( location );
  });
};

locationsController.destroy = function() {
  var self = this;
  Location.findOne( { _id : this.params('id') }, function( err, location ) {
    if ( err ) {
      return self.res.json( { error : "Location does not exist!", msg:err} );
    }
    if ( location ) {
      location.remove( function( err, location ) {
        if ( err ) {
          return self.res.json( { error : "Location could not be removed!", msg:err} );
        }
        return self.res.json( { ok: 1} );
      });
    }
    else {
      return self.res.json( { error : "Location does not exist!"} );
    }
  });
};

locationsController.show = function() {
  var self = this;
  Location.findOne( { _id : this.params('id') }, function( err, location ) {
    if ( err ) {
      return self.res.json( { error : "Location could not be read!", msg:err} );
    }
    if ( location ) {
      return self.res.json( location );
    }
    return self.res.json( { error : "Location could not be found!"} );
  });
};

locationsController.index = function() {
  var self = this;
  Location.find( function( err, locations ) {
    if ( err ) {
      return self.res.json( { error : "Locations could not be read!", msg:err} );
    }
    if ( locations ) {
      return self.res.json( locations );
    }
    return self.res.json( { error : "Locations could not be found!"} );
  });
};


module.exports = locationsController;
