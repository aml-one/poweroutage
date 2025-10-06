#!/usr/bin/python
import psutil
import sys
from subprocess import Popen

for process in psutil.process_iter():
    if process.cmdline() == ['python', '/home/pi/powermanagement/pm.py']:
        sys.exit()

print('Process not found: starting it.')
Popen(['python', '/home/pi/powermanagement/pm.py'])