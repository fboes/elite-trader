/* jshint ignore:start */
var init = require('./init');
var request = require('supertest');
var mongoose = require('mongoose');


describe('index', function() {

    var app;

    before(function(done) {

        init.boot(function(booted) {
            app = booted;
            done();
        });
    });


    function createLocation(name, cb)
    {
      request(app.express)
        .post('/locations')
        .send({"name":name})
        .expect(200)
        .end(function(err, res) {
          should.not.exist(err);
          var location = JSON.parse(res.text);
          location.should.not.have.property('error');
          location.should.have.property('name');
          location.should.have.property('_id');
          location.should.have.property('commodities');
          location.should.have.property('connections');

          location.name.should.equal(name);
          cb(location);

        });

    }

    function createCommodityGroup(name, cb)
    {
      request(app.express)
        .post('/commoditygroups')
        .send({"name":name})
        .expect(200)
        .end(function(err, res) {
          should.not.exist(err);
          var group = JSON.parse(res.text);
          group.should.not.have.property('error');
          group.should.have.property('name');
          group.should.have.property('_id');

          group.name.should.equal(name);
          cb(group);

        });

    }

    function getTraderoute( location, commodity, cb)
    {
      request(app.express)
        .get('/locations/'+location._id+'/commodities/'+commodity._id+"/traderoutes")
        .expect(200)
        .end(function(err, res) {
          console.log(res.text);
          should.not.exist(err);
          var group = JSON.parse(res.text);
          group.should.not.have.property('error');
          group.should.have.property('price');
          group.should.have.property('destination');
          cb(group);

        });

    }

    function createCommodityType(name, group, cb)
    {
      request(app.express)
        .post('/commoditygroups/'+group._id+"/commoditytypes")
        .send({"name":name})
        .expect(200)
        .end(function(err, res) {
          should.not.exist(err);
          var ctype = JSON.parse(res.text);
          ctype.should.not.have.property('error');
          ctype.should.have.property('name');
          ctype.should.have.property('_id');
          ctype.should.have.property('commoditygroup');


          ctype.name.should.equal(name);
          ctype.commoditygroup.should.equal(group._id);
          cb(ctype);

        });

    }

    function createConnection( fromLocation, toLocation, distance, bidirectional, cb ) {
      request(app.express)
        .post('/locations/'+fromLocation._id+'/connections')
        .send({"destination":toLocation._id,"distance":distance})
        .expect(200)
        .end(function(err, res) {
          should.not.exist(err);
          var location = JSON.parse(res.text);
          location.should.not.have.property('error');
          location.should.have.property('name');
          location.should.have.property('_id');
          location.should.have.property('commodities');
          location.should.have.property('connections');

          if ( bidirectional ) {
            return createConnection( toLocation, fromLocation, distance, false, cb );
          } else {
            cb(location);
          }
        });
    }

    function createCommodity( location, ctype, buy, sell, demand, supply, cb ) {
      request(app.express)
        .post('/locations/'+location._id+'/commodities')
        .send({"location_id":location._id,"commoditytype":ctype._id,"buy":buy,"sell":sell,"demand":demand,"supply":supply})
        .expect(200)
        .end(function(err, res) {
          console.log(res.text);
          should.not.exist(err);
          var location = JSON.parse(res.text);
          location.should.not.have.property('error');
          location.should.have.property('name');
          location.should.have.property('_id');
          location.should.have.property('commodities');
          location.should.have.property('connections');

          cb(location);
        });
    }

    it('should create a new commodity group', function(done) {
        var groupname = "dss"
        request(app.express)
          .post('/commoditygroups')
          .send({"name":groupname})
          .expect(200)
          .end(function(err, res) {
            should.not.exist(err);
            var result = JSON.parse(res.text);
            result.should.not.have.property('error');
            result.should.have.property('name');
            result.should.have.property('_id');
            result.name.should.equal(groupname);

            done();
        });
    });

    it('should create three locations with connections to each other and commodities and check for best trade route', function(done) {
      createCommodityGroup("Default", function( group ) {
          createCommodityType("Grain", group, function( ctypeA ) {
            createCommodityType("Fish", group, function( ctypeB ) {
              createCommodityType("Kekse", group, function( ctypeC ) {
                createLocation("Location A", function( locationA ) {
                  createLocation("Location B", function( locationB ) {
                    createLocation("Location C", function( locationC ) {
                      createConnection(locationA, locationB, 5.46, true, function( location ) {
                        createConnection(locationA, locationC, 3.21, true, function( location ) {
                          createConnection(locationB, locationC, 8.92, true, function( location ) {
                            createCommodity( locationA, ctypeA, 16, 12, 1000, 1000, function( locationA ) {
                              createCommodity( locationA, ctypeB, 160, 120, 1000, 1000, function( locationA ) {
                                createCommodity( locationA, ctypeC, 320, 240, 1000, 1000, function( locationA ) {
                                  createCommodity( locationB, ctypeA, 32, 24, 1000, 1000, function( locationB ) {
                                    createCommodity( locationB, ctypeB, 280, 220, 1000, 1000, function( locationB ) {
                                      createCommodity( locationB, ctypeC, 520, 290, 1000, 1000, function( locationB ) {
                                        createCommodity( locationC, ctypeA, 24, 19, 1000, 1000, function( locationC ) {
                                          createCommodity( locationC, ctypeB, 160, 120, 1000, 1000, function( locationC ) {
                                            createCommodity( locationC, ctypeC, 988, 612, 1000, 1000, function( locationC ) {
                                              getTraderoute( locationA, locationA.commodities[0], function(res) {
                                                getTraderoute( locationA, locationA.commodities[1], function(res) {
                                                  getTraderoute( locationA, locationA.commodities[2], function(res) {
                                                    getTraderoute( locationB, locationB.commodities[0], function(res) {
                                                      getTraderoute( locationB, locationB.commodities[1], function(res) {
                                                        getTraderoute( locationB, locationB.commodities[2], function(res) {
                                                          getTraderoute( locationC, locationC.commodities[0], function(res) {
                                                            getTraderoute( locationC, locationC.commodities[1], function(res) {
                                                              getTraderoute( locationC, locationC.commodities[2], function(res) {
                                                                done();

                                                              })

                                                            })

                                                          })

                                                        })

                                                      })

                                                    })

                                                  })

                                                })

                                              })
                                            });
                                          });
                                        });
                                      });
                                    });
                                  });
                                });
                              });
                            });
                          });
                        });
                      });
                    });
                  });
                });
              });
            });
          });
        });
      });

});
