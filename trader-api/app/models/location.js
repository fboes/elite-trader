'use strict';
var mongoose = require('mongoose');
var Schema = mongoose.Schema;


var LocationSchema = new Schema( {
    name: {type: String, index: {unique: true}, required : true},
    connections: [ { destination: { type: Schema.Types.ObjectId, ref: 'Location'},
                     distance : {type:Number} } ],
    commodities: [ { commoditytype : { type: Schema.Types.ObjectId, ref: 'Commoditytype', required: true },
                     sell : { type: Number, default: 0 },
                     buy : { type: Number, default: 9999999 },
                     demand : { type: Number, default: 0 },
                     supply: { type: Number, default: 0 },
    }]
});


var Location = mongoose.model('Location', LocationSchema);

module.exports = Location;
