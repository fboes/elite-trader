'use strict';
var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var Commoditytype = require ('./commoditytype.js')

var CommoditygroupSchema = new Schema( {
    name: {type: String, index: {unique: true}, required : true}
});

var Commoditygroup = mongoose.model('Commoditygroup', CommoditygroupSchema);

CommoditygroupSchema.post('remove', function(commoditygroup) {
  // Remove all commoditytypes of this commoditygroup
  Commoditytype.find(
    { commoditygroup : commoditygroup._id },
    function( err, documents ) {
      documents.forEach( function(e) {
        e.remove();
      });
      //console.log("Deleted",numaffected,"commoditytypes of group",commoditygroup._id);
    }
  );
});

module.exports = Commoditygroup;
