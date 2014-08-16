'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Commoditygroup = require('../models/commoditygroup.js');

var commoditygroupsController = new Controller();

commoditygroupsController.create = function() {
  var commoditygroup = new Commoditygroup( { name : this.param('name') } );
  var self = this;
  commoditygroup.save( function( err ) {
    if ( err ) {
      return self.res.json( { error : "Commoditygroup could not be created!", msg: err } );
    }
    self.res.json( commoditygroup );
  });
};

commoditygroupsController.destroy = function() {
  var self = this;
  Commoditygroup.findOne( { _id : this.params('id') }, function( err, commoditygroup ) {
    if ( err ) {
      return self.res.json( { error : "Commoditygroup does not exist!", msg:err} );
    }
    if ( commoditygroup ) {
      commoditygroup.remove( function( err, commoditygroup ) {
        if ( err ) {
          return self.res.json( { error : "Commoditygroup could not be removed!", msg:err} );
        }
        return self.res.json( { ok: 1} );
      });
    }
    else {
      return self.res.json( { error : "Commoditygroup does not exist!"} );
    }
  });
};

commoditygroupsController.show = function() {
  var self = this;
  Commoditygroup.findOne( { _id : this.params('id') }, function( err, commoditygroup ) {
    if ( err ) {
      return self.res.json( { error : "Commoditygroup could not be read!", msg: err } );
    }
    if ( commoditygroup ) {
      return self.res.json( commoditygroup );
    }
    return self.res.json( { error : "Commoditygroup could not be found!" } );
  });
};

commoditygroupsController.index = function() {
  var self = this;
  Commoditygroup.find( function( err, commoditygroups ) {
    if ( err ) {
      return self.res.json( { error : "Commoditygroups could not be read!", msg: err } );
    }
    if ( commoditygroups ) {
      return self.res.json( commoditygroups );
    }
    return self.res.json( { error : "Commoditygroups could not be found!" } );
  });
};


module.exports = commoditygroupsController;
