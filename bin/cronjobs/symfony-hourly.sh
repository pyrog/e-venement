#!/bin/bash

#EVENEMENT_DIRS='/var/www/e-venement.domain.tld'
EVENEMENT_DIRS=''
EVENEMENT_ROOT_DIRS=/var/www
LOCKFILE_PREFIX=cache/cron

touch /tmp/jypasse.log

if [ -n "$EVENEMENT_ROOT_DIRS" ]; then
for dir in `ls $EVENEMENT_ROOT_DIRS/`; do
if [ -d $EVENEMENT_ROOT_DIRS/$dir ]; then
EVENEMENT_DIRS="$EVENEMENT_DIRS ${dir}"
fi
done
fi

for dir in $EVENEMENT_DIRS; do
  echo $dir
  cd /var/www/$dir
  if [ -x ./symfony ]; then
    [ -e $LOCKFILE.minutely.lock ] && rm $LOCKFILE.minutely.lock
    if [ ! -e $LOCKFILE.hourly.lock ]; then
      touch $LOCKFILE.hourly.lock
      ./symfony e-venement:send-emails --env=prod --application=rp --time-limit=3600 &
      ./symfony e-venement:send-manifestation-notifications
      rm $LOCKFILE.hourly.lock
      touch $LOCKFILE.hourly.quittime
    if
  fi
done
