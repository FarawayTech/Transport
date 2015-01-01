import csv
import json
import subprocess
import os
import re
import unicodedata


def main_import(srv_addr, db_name):
    print "Converting to json"
    f = open('data/line_colors.csv')
    reader = csv.reader(f)

    headers = reader.next()
    locations = {}

    for row in reader:
        line = {}
        for key, value in zip(headers, row):
            line[key] = value
        if not line:
            continue
        location_name = line['location']
        if location_name in locations:
            location = locations[location_name]
        else:
            location = line
            location['location'] = {'type': 'Point', 'coordinates': [float(location.pop('location_lon')), float(location.pop('location_lat')) ]}
            location['lines'] = {}
            locations[location_name] = location
        location['lines'][line.pop('line_num')] = line.pop('color')


    print "Importing into MongoDB at %s/%s" % (srv_addr, db_name)
    from pymongo import MongoClient
    import pymongo
    client = MongoClient(srv_addr)
    db = client[db_name]
    if 'line_colors' in db.collection_names():
        db.drop_collection('line_colors')

    db.line_colors.insert(locations.values())
    print "Creating indexes"
    db.line_colors.ensure_index([("location", pymongo.GEOSPHERE)])
    client.close()
