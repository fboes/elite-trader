'use strict';
var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var UserSchema = {
    name: {type: String, index: {unique: true}},
    ip: String
};
var User = mongoose.model('User', UserSchema);

module.exports = User;
