'use strict';
var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var Location = require('./location.js');


var CommoditytypeSchema = new Schema( {
    name: {type: String, index: {unique: true}, required : true },
    commoditygroup: { type: Schema.Types.ObjectId, ref: 'Commoditygroup', required : true}
});

var Commoditytype = mongoose.model('Commoditytype', CommoditytypeSchema);

CommoditytypeSchema.post('remove', function(commoditytype) {
  // Remove all commodities of this commoditytype
  Location.update(
    {},
    { $pull : { 'commodities' : { commoditytype : commoditytype._id } } },
    { multi : true },
    function( err, numaffected ) {
      console.log("Deleted",numaffected,"commodities of type",commoditytype._id);
    }
  );
});

module.exports = Commoditytype;
