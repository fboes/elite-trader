'use strict';
var mongoose = require('mongoose');
var Schema = mongoose.Schema;


var CommoditytypeSchema = new Schema( {
    name: {type: String, index: {unique: true}, required : true },
    commoditygroup: { type: Schema.Types.ObjectId, ref: 'Commoditygroup', required : true}
});

var Commoditytype = mongoose.model('Commoditytype', CommoditytypeSchema);

module.exports = Commoditytype;
