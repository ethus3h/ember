#!/usr/bin/env python
# -*- coding: utf-8 -*-
#From https://gist.github.com/endolith/157847 . Edited 8 September 2013.
#Version 1.1, 12 September 2013.
# Use the KDE screen locker to enforce bedtime or otherwise lock out the computer, and set an away message in Pidgin

# Note that the actual time it warns the user is whenever you call the script,
# so you can use more granularity than an hour, can set it for different times 
# on different days, etc.

# You can enter your password and log back in if you want, but it will repeatedly lock you back out.

import time
import pynotify
from subprocess import Popen, PIPE

appname      = 'Bedtime' # In case you want to use it for something else
bedtime      = 12 + 9    # (hours) Time before which the script should refuse to run
morning      = 5         # (hours) Time after which the script should quit and stop locking out the user
grace_period = 10        # (minutes) Time between warning and locking out
snooze_time  = 1        # (minutes) Time between logging back in and being locked back out
away_message = 'enforced bedtime'

def current_hour():
    """Returns the current hour"""
    return time.localtime().tm_hour

def notify(message):
    """Pops up a libnotify notification and prints"""
    print message
    pynotify.Notification(appname, message).show()

if morning < current_hour() < bedtime:
    print 'Script started too early - quitting'
else:
    pynotify.init(appname)
    notify('You will be locked out in %(grace_period)s minutes' % locals())
    time.sleep((grace_period - snooze_time) * 60)    
   
    while current_hour() >= bedtime or current_hour() < morning:
        status = Popen(['/usr/lib/kde4/libexec/kscreenlocker', '--forcelock'], stdout=PIPE).communicate()[0]
        print status.split('\n')[0]
        if 'inactive' in status:
            notify('You will be locked out in %(snooze_time)s minutes' % locals())
            time.sleep(snooze_time * 60)
            
            Popen(['purple-remote', 'setstatus?status=away&message=' + away_message])
            notify('Locking screen...')
            time.sleep(4) # short pause so you see the notification that tells you why it's shutting down
            Popen(['/usr/lib/kde4/libexec/kscreenlocker', '--forcelock'])
        time.sleep(snooze_time * 60)

