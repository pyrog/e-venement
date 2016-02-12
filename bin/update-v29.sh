#!/bin/bash

#**********************************************************************************
#
#	    This file is part of e-venement.
# 
#    e-venement is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License.
# 
#    e-venement is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
# 
#    You should have received a copy of the GNU General Public License
#    along with e-venement; if not, write to the Free Software
#    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
# 
#    Copyright (c) 2006-2015 Baptiste SIMON <baptiste.simon AT e-glop.net>
#    Copyright (c) 2006-2015 Libre Informatique [http://www.libre-informatique.fr/]
# 
#**********************************************************************************/

# preconditions
[ ! -d "data/sql" ] && echo "cd to your project's root directory please" && exit 3;

[ -z "$1" ] && echo "You must specify the DB user that is used by e-venement as the first parameter" && exit 1
SFUSER="$1"
[ -n "$2" ] && export PGDATABASE="$2"
[ -n "$3" ] && export PGUSER="$3"
[ -n "$4" ] && export PGHOST="$4"
[ -n "$5" ] && export PGPORT="$5"


echo "Usage: bin/update-v29.sh SFUSER [DB [USER [HOST [PORT]]]]"
echo "Are you sure you want to continue with those parameters :"
echo "The e-venement's DB user: $SFUSER"
echo "Database: $PGDATABASE"
echo "User: $PGUSER"
echo "Host: $PGHOST"
echo "Port: $PGPORT"
echo ""
echo "To continue press ENTER"
echo "To cancel press CTRL+C NOW !!"
read

echo "DUMPING DB..."
pg_dump -Fc > data/sql/$name-`date +%Y%m%d`.update.before.pgdump && echo "DB pre dumped"

## preliminary modifications & backup
psql <<EOF
CREATE TABLE cache (
    id bigint NOT NULL,
    content bytea,
    domain character varying(255),
    identifier character varying(255),
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    version bigint
);
EOF

./symfony cc
./symfony doctrine:build-model
last=$?
./symfony cc
[ $last -eq 0 ] && ./symfony doctrine:build-forms
last=$?
./symfony cc
[ $last -eq 0 ] && ./symfony doctrine:build-filters
last=$?
./symfony cc
if [ ! $? -eq 0 ]
then
  echo "";
  echo "  ... failed."
  exit 255
fi

  ./symfony e-venement:search-index Contact
  ./symfony e-venement:search-index Organism
fi

echo "Your update went good. Finish the process testing your software and let's go!!"
