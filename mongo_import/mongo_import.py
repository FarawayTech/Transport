#!/usr/bin/env python
# mongo_import.py SERVER_ADDRESS DB_NAME USER PASSWORD

import sys
SRV_ADDR, DB_NAME = sys.argv[1:]

from scripts import import_sms_tickets, import_stations

print "Importing stations"
import_stations.main_import(SRV_ADDR, DB_NAME)

print "Importing SMS ticket codes"
import_sms_tickets.main_import(SRV_ADDR, DB_NAME)