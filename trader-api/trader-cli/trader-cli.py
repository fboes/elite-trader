#! /usr/bin/env python
import time
import logging
import argparse
import ConfigParser
import json
import os
import re
import requests

# Read config
parser = argparse.ArgumentParser(description='Simple interactive Python command line interface for the Trader API')

parser.add_argument("--config", type=str, help="The config file to use", default="~/trader-cli.conf", metavar="FILE")

args,remaining_args = parser.parse_known_args()

defaults = {
    'endpoint' : 'localhost:3000'
}

if args.config:
  try:
    config = json.load(open(args.config))
    defaults.update(config)
  except:
    pass


parser.set_defaults(**defaults)

parser.add_argument("--endpoint", help="Trader API endpoint", type=str)
parser.add_argument("--logfile", help="Logfile", type=str)
parser.add_argument("--authuser", help="User name", type=str)
parser.add_argument("--authpwd", help="Password", type=str)

subparser = parser.add_subparsers(help="Action to perform")
update_commodity_parser = subparser.add_parser('update_commodity',help='Sets new commodity data within a location')


args = parser.parse_args(remaining_args)

endpoint = args.endpoint
authuser = args.authuser
authpwd = args.authpwd
logfile = args.logfile


logging.basicConfig(filename=logfile, level=logging.INFO, format='%(asctime)-15s %(message)s')

logging.info("Start...")
logging.info(args)

# Fetch and list all Locations, Commoditytypes and -groups
locations = requests.get("http://"+endpoint+"/locations").json()
commoditytypes = requests.get("http://"+endpoint+"/commoditytypes").json()
commoditygroups = requests.get("http://"+endpoint+"/commoditygroups").json()

#logging.info(locations)
#logging.info(commoditytypes)
#logging.info(commoditygroups)

# Let the user select a location
for location in locations:
  print "[%s] %s" % (locations.index(location),location['name'])

def updatecommodity( commodity, buy, sell, demand, supply ):
  if commodity:
    # Update
    payload = { "buy" : buy, "sell" : sell, "demand" : demand, "supply" : supply }
    headers = {'content-type':'application/json'}
    r = requests.put("http://"+endpoint+"/locations/"+location['_id']+"/commodities/"+commodity['_id'],
      auth=(authuser,authpwd),
      data=json.dumps(payload),
      headers=headers)
    print "Status:",r.status_code
    #logging.info(r.text)
  else:
    # Create new
    payload = { "commoditytype" : commoditytype['_id'], "buy" : buy, "sell" : sell, "demand" : demand, "supply" : supply }
    headers = {'content-type':'application/json'}
    r = requests.post("http://"+endpoint+"/locations/"+location['_id']+"/commodities",
      auth=(authuser,authpwd),
      data=json.dumps(payload),
      headers=headers)

    print "Status:",r.status_code


valid = False
while not valid:
  var = raw_input("Enter number of location: " )
  try:
    location = locations[int(var)]
    valid = True
  except:
    print "Invalid input!"

def find(lst, key, value):
  for i, dic in enumerate(lst):
    if dic[key] == value:
      return dic
  return None

# List all commoditytypes with their prices
for commoditytype in commoditytypes:

  buy = 999999
  sell = 0
  demand = 0
  supply = 0

  location_commodity = find( location['commodities'], 'commoditytype',  commoditytype['_id'] ) # next(d for (index,d) in enumerate(location['commodities']) if d['commoditytype'] == commoditytype['_id'] )

  if location_commodity:
    buy = location_commodity['buy']
    sell = location_commodity['sell']
    demand = location_commodity['demand']
    supply = location_commodity['supply']

  print "[%s] %s sell:%s buy:%s demand:%s supply:%s" % (commoditytypes.index(commoditytype),commoditytype['name'],sell,buy,demand,supply)
interactivemode = False
valid = False
while not valid:
  var = raw_input("Enter number of commodity followed by sell buy separated by blanks, or i for interactive mode: " )
  if var == 'i':
    valid = True
    interactivemode = True
  else:
    try:
      splitstr = var.split(" ")
      commoditytype = commoditytypes[int(splitstr[0])]
      commodity = find( location['commodities'], 'commoditytype', commoditytype['_id'] )
      buy = int(splitstr[2])
      sell = int(splitstr[1])
      #demand = int(splitstr[3])
      #supply = int(splitstr[4])

      valid = True
    except:
      print "Invalid input!"
      raise

if interactivemode:
  print "Entering interactive mode"
  for commoditytype in commoditytypes:
    var = raw_input("Enter sell price for "+commoditytype['name']+" (empty for unchanged):" )
    try:
      sell = int(var)
      commodity = find( location['commodities'], 'commoditytype', commoditytype['_id'] )
      updatecommodity( commodity, buy, sell, demand, supply )
    except:
      pass
else:
  updatecommodity( commodity, buy, sell, demand, supply )
