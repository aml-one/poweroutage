#!/usr/bin/python
from datetime import datetime, timedelta, date
import sqlite3
import json
import time
import requests

def readDataBase():
    conn = sqlite3.connect('powrmngmnt.db')
    cur = conn.cursor()

    #create table if not exist
    cur.execute('''CREATE TABLE IF NOT EXISTS log (id TEXT PRIMARY KEY, event TEXT, time TEXT, microsec TEXT, valid TEXT)''')

    #clean up old entries from database to keep it nice and small in size
    #keep only the last 7 days worth of entries
    s = date.today()
    a_week_earlier_date = s + timedelta(days=-7)
    cur.execute("DELETE from log where time LIKE '%" + a_week_earlier_date.strftime("%Y-%m-%d") + "%'")

    cur.execute("SELECT * from log where time LIKE '%" + datetime.now().strftime("%Y-%m-%d") + "%'")
    result = cur.fetchall()

    json_data = json.dumps(result)
    #print(json_data)

    conn.close()

    headers = {"Content-Type": "application/fhir+json;charset=utf-8"}
    url = 'https://WEBADDRESS.COM/lib/_powerOutage_monitor.php'

    myjson = {"data": json_data}

    r = requests.post(url, headers = headers,  json = myjson)

starttime = time.monotonic()
while True:
    readDataBase()

    headers = {"Content-Type": "application/fhir+json;charset=utf-8"}
    url = 'https://WEBADDRESS.COM/lib/_powerOutage_readerhealth.php'
    pingobj = {"ping": datetime.now().strftime("%Y-%m-%d %H:%M:%S")}
    r = requests.post(url, headers = headers,  json = pingobj)

    time.sleep(20.0 - ((time.monotonic() - starttime) % 20.0))