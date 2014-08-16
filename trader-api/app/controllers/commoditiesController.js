'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Location = require('../models/location.js'),
    Commoditytype = require('../models/commoditytype.js'),
    passport = require('passport');


var commoditiesController = new Controller();

commoditiesController.before('*', function( next ) {
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

commoditiesController.update = function() {
  var self = this;
  var commodityid = self.params('id');




  // Location available?
  if ( ! this._location ) {
    return self.res.json( { error : "Location not found!" } );
  }

  // Check for existence first - we will only support updating existing elements!
  var commodityindex = -1;
  for ( var i = 0; i<self._location.commodities.length; ++i ) {
    if ( String(self._location.commodities[i]._id) == commodityid ) {
      commodityindex = i;
      break;
    }
  }

  if ( commodityindex != -1 ) {
    var e = self._location.commodities[i];
    e.sell = self.params('sell') || e.sell;
    e.buy = self.params('buy') || e.buy;
    e.demand = self.params('demand') || e.demand;
    e.supply = self.params('supply') || e.supply;
  } else {
    return self.res.json( { error : "Commodity is not in list ! Maybe you want to create instead?" } );
  }


  self._location.save(
    function( err, location ) {
      if (err) {
        return self.res.json( { error : "Commodity could not be modified!", msg: err } );
      }
      return self.res.json( location );
  });

}

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
