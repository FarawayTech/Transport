#!/usr/bin/env python
# mongo_import.py SERVER_ADDRESS DB_NAME USER PASSWORD

import csv
import json
import subprocess
import os
import re
import unicodedata
import sys

def strip_accents(s):
    s = s.decode('utf-8')
    return ''.join(c for c in unicodedata.normalize('NFD', s)
                   if unicodedata.category(c) != 'Mn')

SRV_ADDR, DB_NAME, USER, PSWD = sys.argv[1:]

REPLACE_PUNCT = re.compile(r'[.()/,\-&]')
STATION_NAMES = 'stop_name,ch_station_long_name,ch_station_synonym1,ch_station_synonym2,ch_station_synonym3,ch_station_synonym4'.split(',')

try:
    os.mkdir("temp_import")
except:
    pass
#subprocess.call(["curl http://gtfs.geops.ch/dl/complete/stops.txt > temp_import/stops.csv"], shell=True)


f = open('temp_import/stops.csv')
out = open('temp_import/stops.json', 'w')
reader = csv.reader(f)

headers = reader.next()
stations = []
station_ids = set()

for row in reader:
    station = {}
    for key, value in zip(headers, row):
        if key=='stop_id':
            value = value.split(':')[0]
        station[key] = value
    if station['stop_id'] in station_ids:
        continue
    station_ids.add(station['stop_id'])
    station['location'] = {'type': 'Point', 'coordinates': [float(station.pop('stop_lon')), float(station.pop('stop_lat')) ]}
    station['canonical_name'] = station['stop_name']

    # create text field for station names
    names_set = set()
    names = []
    for station_attr in STATION_NAMES:
        for name in REPLACE_PUNCT.sub(' ', strip_accents(station.pop(station_attr))).split():
            if name.lower() not in names_set:
                names.append(name.lower())
                names_set.add(name.lower())
    station['names'] = names

    # create text field for station name prefixes
    names_set = set()
    prefix_names = []
    for name in names:
        for i in range(1, len(name)+1):
            prefix_name = name[:i]
            if prefix_name not in names_set:
                prefix_names.append(prefix_name)
                names_set.add(prefix_name)
    station['prefix_names'] = prefix_names

    stations.append(station)

json.dump(stations, out, indent=1)
f.close()
out.close()


from pymongo import MongoClient
import pymongo
client = MongoClient()
db = client[DB_NAME]
if 'stops' in db.collection_names():
    db.drop_collection('stops')
#
client.close()
subprocess.call(["mongoimport -h {0} -d {1} -c stops --file temp_import/stops.json --jsonArray".format(SRV_ADDR, DB_NAME, USER, PSWD)], shell=True)
subprocess.call(['mongo {0}/{1} mongo_index.js'.format(SRV_ADDR, DB_NAME, USER, PSWD)], shell=True)
