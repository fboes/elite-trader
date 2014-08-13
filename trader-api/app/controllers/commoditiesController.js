'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Location = require('../models/location.js'),
    Commoditytype = require('../models/commoditytype.js');


var commoditiesController = new Controller();

commoditiesController.before('*', function( next ) {
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

commoditiesController.create = function() {
  var self = this;

  // Location available?
  if ( ! this._location ) {
    return self.res.json( { error : "Location not found!" } );
  }

  // Commodity already exists?
  for ( var i = 0; i<self._location.commodities.length; ++i ) {
    if ( self._location.commodities[i].commoditytype == self.params('commoditytype') ) {
      return self.res.json( { error : "Commoditytype already in list! Maybe you want to update instead?" } );
    }
  }


  // Check for existence of commoditytype
  Commoditytype.findOne( { _id : this.params('commoditytype') }, function( err, commoditytype ) {
    if ( err || ! commoditytype ) {
      return self.res.json( { error : "Commoditytype not found!" } );
    }
    Location.findOneAndUpdate(
      { _id : self.params('location_id') },
      { $push : { commodities : {
        commoditytype : self.params('commoditytype'),
        sell : self.params('sell'),
        buy : self.params('buy'),
        demand : self.params('demand'),
        supply : self.params('supply')
        } } },
      function( err, location ) {
        if (err) {
          return self.res.json( { error : "Commodity could not be added to location!", msg: err } );
        }
        return self.res.json( location );
    });

  });


};

commoditiesController.index = function() {
  var self = this;
  self.res.json( self._location.commodities );
};

commoditiesController.show = function() {
  var self = this;
  self.res.json( { error : "Not yet implemented!" } );

};

module.exports = commoditiesController;
