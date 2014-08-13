'use strict';
var mongoose = require('mongoose');
var Schema = mongoose.Schema;


var CommoditygroupSchema = new Schema( {
    name: {type: String, index: {unique: true}, required : true}
});

var Commoditygroup = mongoose.model('Commoditygroup', CommoditygroupSchema);

module.exports = Commoditygroup;
