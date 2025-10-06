#!/usr/bin/python
import RPi.GPIO as GPIO
import time
from datetime import datetime
import sqlite3
import sys
import requests

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(2, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def trigger_event(channel):
    dateStr = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    dateStrId = datetime.now().strftime("%Y%m%d%H%M%S")
    microtime = datetime.now().strftime("%-S%f")

    # Read the state of the pin
    pin_state = GPIO.input(2)

    # Check if the state is HIGH (1)
    if pin_state == GPIO.HIGH:
        # HIGH = POWER ON
        writeDataBase("1" + dateStrId, "on", dateStr, microtime, "1")
    else:
        # LOW = POWER LOSS
        writeDataBase("0" + dateStrId, "off", dateStr, microtime, "1")


def writeDataBase(eid, eventStr, timeStr, microsec, valid):
    conn = sqlite3.connect('powrmngmnt.db')
    cur = conn.cursor()

    cur.execute('''CREATE TABLE IF NOT EXISTS log (id TEXT PRIMARY KEY, event TEXT, time TEXT, microsec TEXT, valid TEXT)''')

    cur.execute("SELECT * FROM log ORDER BY rowid desc LIMIT 1")
    results = cur.fetchall()

    if eventStr not in results:
        cur.execute("INSERT OR IGNORE INTO log VALUES ( '" + eid + "', '" + eventStr + "', '" + timeStr + "', '" + microsec + "', '" + valid + "')")

    if eventStr in results:
        cur.execute("INSERT OR IGNORE INTO log VALUES ( '" + eid + "', '" + eventStr + "', '" + timeStr + "', '" + microsec + "', '0')")

    conn.commit()
    conn.close()


GPIO.add_event_detect(2, GPIO.BOTH, callback=trigger_event, bouncetime=100)

while True:
    time.sleep(0.5)

    if datetime.now().strftime("%S") in ("00", "20", "40"):
        headers = {"Content-Type": "application/fhir+json;charset=utf-8"}
        url = 'https://WEBADDRESS.COM/lib/_powerOutage_pmhealth.php'
        pingobj = {"ping": datetime.now().strftime("%Y-%m-%d %H:%M:%S")}
        r = requests.post(url, headers = headers,  json = pingobj)