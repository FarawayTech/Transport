import csv
import json
import subprocess
import os
import re
import unicodedata


def main_import(srv_addr, db_name):
    print "Converting to json"
    f = open('data/sms_tickets.csv')
    reader = csv.reader(f)

    headers = reader.next()
    sms_locations = {}

    for row in reader:
        location = {}
        for key, value in zip(headers, row):
            location[key] = value
        if not location:
            continue
        main_code = location['main_code']
        if main_code in sms_locations:
            sms_location = sms_locations[main_code]
        else:
            location['localities'] = []
            sms_location = location
            sms_locations[main_code] = sms_location
        sms_location['localities'].append({'name': location.pop('locality'), 'zone': location.pop('zone')})


    print "Importing into MongoDB at %s/%s" % (srv_addr, db_name)
    from pymongo import MongoClient
    import pymongo
    client = MongoClient(srv_addr)
    db = client[db_name]
    if 'sms_tickets' in db.collection_names():
        db.drop_collection('sms_tickets')

    db.sms_tickets.insert(sms_locations.values())
    print "Creating indexes"
    db.sms_tickets.ensure_index('localities.name')
    client.close()
