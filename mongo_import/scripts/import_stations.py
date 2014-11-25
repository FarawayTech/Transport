import csv
import json
import subprocess
import os
import re
import unicodedata

def strip_accents(s):
    s = s.decode('utf-8')
    return ''.join(c for c in unicodedata.normalize('NFD', s)
                   if unicodedata.category(c) != 'Mn')

REPLACE_PUNCT = re.compile(r'[.()/,\-&]')
STATION_NAMES = 'stop_name,ch_station_long_name,ch_station_synonym1,ch_station_synonym2,ch_station_synonym3,ch_station_synonym4'.split(',')


def main_import(srv_addr, db_name):
    try:
        os.mkdir("temp_import")
    except:
        pass
    print "Downloading stops.csv"
    subprocess.call(["curl http://gtfs.geops.ch/dl/complete/stops.txt > temp_import/stops.csv"], shell=True)


    print "Converting to json"
    f = open('temp_import/stops.csv')
    reader = csv.reader(f)

    headers = reader.next()
    stations = []
    station_ids = set()

    for row in reader:
        station = {'weight': 0}
        for key, value in zip(headers, row):
            if key=='stop_id':
                value = value.split(':')[0]
            station[key] = value
        if station['stop_id'] in station_ids:
            stations[-1]['weight'] += 1
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

    print "Importing into MongoDB at %s/%s" % (srv_addr, db_name)
    from pymongo import MongoClient
    import pymongo
    client = MongoClient(srv_addr)
    db = client[db_name]
    if 'stops' in db.collection_names():
        db.drop_collection('stops')

    db.stops.insert(stations)
    print "Creating indexes"
    db.stops.ensure_index([("location", pymongo.GEOSPHERE)])
    db.stops.ensure_index('names')
    db.stops.ensure_index('prefix_names')
    client.close()
