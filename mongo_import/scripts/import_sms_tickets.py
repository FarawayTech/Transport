import csv
import json
import subprocess
import os
import re
import unicodedata


def main_import(srv_addr, db_name):
    print "Converting to json"
    f = open('data/sms_tickets.csv')
    out = open('temp_import/sms_tickets.json', 'w')
    reader = csv.reader(f)

    headers = reader.next()
    sms_locations = {}

    for row in reader:
        location = {}
        for key, value in zip(headers, row):
            location[key] = value
        if not location:
            continue
        sms_number = location['sms_number']
        if sms_number in sms_locations:
            sms_location = sms_locations[sms_number]
        else:
            location['localities'] = []
            sms_location = location
            sms_locations[sms_number] = sms_location
        sms_location['localities'].append({'name': location.pop('locality'), 'zone': location.pop('zone')})
