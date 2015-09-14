#!/bin/bash

CONN=`grep dsn config/databases.yml | grep -v '^#' | sed "s/\s*dsn:\s*'\(.*\):host=\(.*\);dbname=\(\w*\).*/\1:\2:\3/g"`

if [ "`echo $CONN | cut -d : -f 1`" = 'pgsql' ]; then
  CMD=psql
fi

HOST=''
if [ ! "`echo $CONN | cut -d : -f 2`" = 'localhost' ]; then
  HOST=`echo $CONN | cut -d : -f 2`
fi

$CMD $HOST `echo $CONN | cut -d : -f 3`
