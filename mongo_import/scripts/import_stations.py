import csv
import json
import subprocess
import os
import re
import unicodedata
import shutil

def strip_accents(s):
    s = s.decode('utf-8')
    return ''.join(c for c in unicodedata.normalize('NFD', s)
                   if unicodedata.category(c) != 'Mn')

REPLACE_PUNCT = re.compile(r'[.()/,\-&]')
STATION_NAMES = ['stop_name','ch_station_long_name','ch_station_synonym1','ch_station_synonym2','ch_station_synonym3','ch_station_synonym4']


def main_import(srv_addr, db_name):
    try:
        os.mkdir("temp_import")
    except:
        pass

    PREV_COUNT = sum(1 for line in open("temp_import/stops.csv"))
    shutil.move("temp_import/stops.csv", "temp_import/stops_old.csv")
    print("Downloading stops file...\t")
    subprocess.call(["curl http://gtfs.geops.ch/dl/complete/stops.txt > temp_import/stops.csv"], shell=True)
    print "[OK]"
    CUR_COUNT = sum(1 for line in open("temp_import/stops.csv"))
    if CUR_COUNT + 10 < PREV_COUNT:
        print "New file contains %d stations less than the old one, aborting\t[FAIL]" % (PREV_COUNT-CUR_COUNT)
    subprocess.call(["cat temp_import/stops.csv | sort -r -k1,1 -t',' > temp_import/stops_sorted.csv"], shell=True)


    print "Converting to json"
    f = open('temp_import/stops_sorted.csv')
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
        
        # synonyms, used in weighted matching
        station['synonyms'] = [station[syn_attr_name] for syn_attr_name in STATION_NAMES if station[syn_attr_name]]

        def recursive_prefixes(prefix_set, sequence):
            for i in range(1, len(sequence)):
                # if space - recurse
                if sequence[i] == ' ':
                    recursive_prefixes(prefix_set, sequence[i+1:])
                else:
                    prefix_set.add(sequence[:i].strip())
            prefix_set.add(sequence)

        # create text field for station names
        normal_names = set()
        for station_attr in STATION_NAMES:
            normal_name = ' '.join(REPLACE_PUNCT.sub(' ', strip_accents(station.pop(station_attr))).split()).lower()
            if normal_name:
                normal_names.add(normal_name)

        station['first_names'] = list(set([name[:i+1].strip() for name in normal_names for i in range(len(name))]))

        # create text field for station name prefixes
        second_names = set()
        for normal_name in list(normal_names):
            second_name = ' '.join(normal_name.split()[1:]) or normal_name
            recursive_prefixes(second_names, second_name)

        station['second_names'] = list(second_names)

        # all name prefixes
        prefix_names = set()
        for normal_name in list(normal_names):
            for name in normal_name.split():
                for i in range(len(name)):
                    prefix_names.add(name[:i+1])
        station['prefix_names'] = list(prefix_names)

        stations.append(station)

    print "Importing into MongoDB at %s/%s (temp collection)" % (srv_addr, db_name)
    from pymongo import MongoClient
    import pymongo
    client = MongoClient(srv_addr)
    temp_col = 'temp_stops'
    db = client[db_name]
    temp_col = db['temp_stops']

    temp_col.insert(stations)
    print "Creating indexes"
    temp_col.ensure_index([("location", pymongo.GEOSPHERE)])
    temp_col.ensure_index('stop_id')
    temp_col.ensure_index('first_names')
    temp_col.ensure_index('second_names')
    temp_col.ensure_index('prefix_names')
    temp_col.ensure_index('weight')

    print 'Dropping old collection and renaming'
    if 'stops' in db.collection_names():
        db.drop_collection('stops')
    temp_col.rename('stops')
    client.close()
