'use strict';

var locomotive = require('locomotive');
var Controller = locomotive.Controller,
    Location = require('../models/location.js'),
    Commoditytype = require('../models/commoditytype.js');

var traderoutesController = new Controller();

traderoutesController.getLocationById = function( location_id, alllocations ) {
  for( var i = 0; i < alllocations.length; ++i ) {
    if ( String(alllocations[i]._id) == String(location_id) ) {
      return alllocations[i];
    }
  }
}

traderoutesController.checkBestProfitPerHop = function( location_id, commoditytype, current_bestprofit_per_hop, current_hops, max_hops, visitedlocations, alllocations, original_buy_price )
{
  console.log("checkBestProfitPerHop",location_id);

  if ( current_hops > max_hops ) { return null; }
  if ( visitedlocations[location_id] && visitedlocations[location_id] <= current_hops ) { return null; }

  visitedlocations[location_id] = current_hops;

  var location = traderoutesController.getLocationById( location_id, alllocations );

  console.log("Visiting",location.name," (current_hops",current_hops,")");
  for( var i = 0; i < location.connections.length; ++i ) {
    var checkresult = traderoutesController.checkBestProfitPerHop(
      location.connections[i].destination,
      commoditytype,
      current_bestprofit_per_hop,
      current_hops+1,
      max_hops,
      visitedlocations,
      alllocations,
      original_buy_price
    );

    if ( checkresult && ( checkresult.price_per_hop_index > current_bestprofit_per_hop.price_per_hop_index ) ) {
      current_bestprofit_per_hop = checkresult;
    }

  }

console.log("Checking local commodities of",location.name,commoditytype);
  for( var i = 0; i < location.commodities.length; ++i ) {
    console.log("Checking...",location.commodities[i].commoditytype);

    if ( String(location.commodities[i].commoditytype) == String(commoditytype) ) {
      console.log("Profit here is...",( location.commodities[i].sell - original_buy_price )," or ",( location.commodities[i].sell - original_buy_price ) / current_hops,"per hop");
      if ( current_bestprofit_per_hop.price_per_hop_index < ( ( location.commodities[i].sell - original_buy_price ) / current_hops ) ) {
        current_bestprofit_per_hop = { price_per_hop_index : ( ( location.commodities[i].sell - original_buy_price ) / current_hops ), destination : location_id, price : location.commodities[i].sell };
      }
    }
  }

  return current_bestprofit_per_hop;

}

traderoutesController.show = function() {
  var self = this;
  // For now, needs a location and commoditytype
  var location_id = this.params('location_id');
  var commodity_id = this.params('commodity_id');

  Location.findOne( { _id : location_id }, function( err, location ) {
    if ( err ) {
      return self.res.json( { error : "Location could not be read!", msg:err} );
    }
    if ( !location ) {
      return self.res.json( { error : "Location could not be found!"} );
    }

    // What's the commodity type?
    var commoditytype_id = undefined;
    var current_bestprofit_per_hop = { price : 0, destination : location_id };
    var original_buy_price = 0;
    for ( var i = 0; i<location.commodities.length; ++i ) {
      if ( location.commodities[i]._id == commodity_id ) {
        commoditytype_id = location.commodities[i].commoditytype;
        original_buy_price = location.commodities[i].buy;
        current_bestprofit_per_hop.price_per_hop_index = location.commodities[i].sell - original_buy_price;
      }
    }

    Commoditytype.findOne( { _id : commoditytype_id }, function( err, commoditytype ) {
      if ( err ) {
        return self.res.json( { error : "Commoditytype could not be read!", msg: err } );
      }
      if ( commoditytype ) {
        // "profit per hop" is 0
        // Hops is 1
        // Commodity price is the current buy price at the starting location
        // Visited locations = []

        // ##### Le Recursive( location, price, commodity, hops ):
        // Within Location...
          // For each connection.destination not in visited locations....
            // Hops += 1
            // Fetch price for commodity..
            // Is the "profit per hop" better than the current one? Save it!
            // Le Recursive( connection.destination, price, commodity )
        Location.find( function( err, locations ) {
          if ( err ) {
            return self.res.json( { error : "Locations could not be read!", msg:err} );
          }
          if ( locations ) {
            var bestprofitperhop = traderoutesController.checkBestProfitPerHop(
              location_id,
              commoditytype_id,
              current_bestprofit_per_hop,
              2,
              5,
              {},
              locations,
              original_buy_price
            );
            return self.res.json( bestprofitperhop );
          }
          else {
            return self.res.json( { error : "Locations could not be found!"} );

          }
        });
      }
      else {
        return self.res.json( { error : "Commoditytype could not be found!" } );

      }
    });


  });

};

module.exports = traderoutesController;
