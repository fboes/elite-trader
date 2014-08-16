'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Commoditytype = require('../models/commoditytype.js'),
    Commoditygroup = require('../models/commoditygroup.js');


var commoditytypesController = new Controller();

commoditytypesController.before('*', function( next ) {
  var self = this;
  if ( this.params('commoditygroup_id') ) {
    Commoditygroup.findOne( { _id : this.params('commoditygroup_id') }, function( err, commoditygroup ) {
      if ( err ) { return next(err); }
      self._commoditygroup = commoditygroup;
      return next();
    });
  }
  else {
    return next();
  }
});

commoditytypesController.destroy = function() {
  var self = this;
  Commoditytype.findOne( { _id : this.params('id') }, function( err, commoditytype ) {
    if ( err ) {
      return self.res.json( { error : "Commoditytype does not exist!", msg:err} );
    }
    if ( commoditytype ) {
      commoditytype.remove( function( err, commoditytype ) {
        if ( err ) {
          return self.res.json( { error : "Commoditytype could not be removed!", msg:err} );
        }
        return self.res.json( { ok: 1} );
      });
    }
    else {
      return self.res.json( { error : "Commoditytype does not exist!"} );
    }
  });
};

commoditytypesController.create = function() {
  var commoditytype = new Commoditytype( { name : this.param('name'), commoditygroup : this._commoditygroup } );
  var self = this;
  commoditytype.save( function( err ) {
    if ( err ) {
      return self.res.json( { error : "Commoditytype could not be created!", msg: err } );
    }
    self.res.json( commoditytype );
  });
};

commoditytypesController.show = function() {
  var self = this;
  Commoditytype.findOne( { _id : this.params('id') }, function( err, commoditytype ) {
    if ( err ) {
      return self.res.json( { error : "Commoditytype could not be read!", msg: err } );
    }
    if ( commoditytype ) {
      return self.res.json( commoditytype );
    }
    return self.res.json( { error : "Commoditytype could not be found!" } );
  });
};

commoditytypesController.index = function() {
  var self = this;
  console.log("Huh?");
  Commoditytype.find( function( err, commoditytypes ) {
    if ( err ) {
      return self.res.json( { error : "Commoditytypes could not be read!", msg: err } );
    }
    if ( commoditytypes ) {
      return self.res.json( commoditytypes );
    }
    return self.res.json( { error : "Commoditytypes could not be found!" } );
  });
};


module.exports = commoditytypesController;
