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

echo "Usage: bin/mirate-to-git.sh OLD_SVN_DIRECTORY [go]"

# preconditions
[ ! -d "data/sql" ] && echo "cd to your project's root directory please" && exit 3;
[ ! -e ".gitmodules" ] && echo "cd to your project's root directory please" && exit 3;
NEWDIR=`pwd`

[ -z "$1" ] && echo "You must specify the directory used previously to be able to get back required data/setup" && exit 1
cd $1
OLDDIR=`pwd`

[ ! -d "$OLDDIR/data/sql" ] && echo "You must provide a correct directory instead of '$OLDDIR'" && exit 2;

CP="echo -n cp"
GIT="echo git"
CHMOD="echo chmod"
GO=""
SF="echo ./symfony"
[ ! -z "$2" ] && [ "$2" == 'go' ] && \
CP=cp     && \
GO=yes    && \
GIT=git   && \
SF=./symfony && \
CHMOD=chmod

echo ""
echo "Are you sure you want to continue with those parameters :"
echo "The old e-venement directory, using SVN or no CVS at all is $OLDDIR"
echo "The new e-venement directory, using GIT is $NEWDIR"
[ "$2" == 'go' ] && echo "This IS NOT a TEST procedure... You will process it for real!"
echo ""
echo "To continue press ENTER"
echo "To cancel press CTRL+C NOW !!"
read


for elt in `find $OLDDIR -iname '*.template'`; do
  NEW=`echo $elt | sed 's/.template$//g'`;
  NEW=`echo "$NEW" | sed "s!^${OLDDIR}/*!!g"`
  if [ -e $NEW ]; then
    echo -n "Copying $NEW ... "
    $CP $OLDDIR/$NEW $NEWDIR/$NEW && \
    echo " ok"
  fi
done

echo ""
echo -n "Copying specific files ... "
$CP -a $OLDDIR/web/uploads/* $NEWDIR/web/uploads && \
$CP -a $OLDDIR/web/private/* $NEWDIR/web/private && \
[ -e $OLDDIR/config/private ] && $CP -r $OLDDIR/config/private $NEWDIR/config
echo " ok"

cd $NEWDIR

echo ""
read -p "Do you want to complete your GIT deployment? (Y/n) " deploy
if [ "$deploy" != "n" ]; then
  $GIT pull
  $GIT submodule init
  $GIT submodule update
fi

echo ""
read -p "Do you want to fix the needed file permissions for a correct usage of Symfony? (Y/n) " reset
if [ "$reset" != 'n' ]; then
  $CHMOD -R a+w web/uploads cache log
fi

echo ""
read -p "Do you want to re-build your Symfony model/forms/filters (needed)? (Y/n) " rebuild
if [ "$reset" != 'n' ]; then
  $SF doctrine:build-model
  $SF cc
  $SF doctrine:build-forms
  $SF cc
  $SF doctrine:build-filters
  $SF cc
fi

echo ""
echo "To finish this migration to GIT, DO NOT FORGET:"
echo "- modifying if required the DOCUMENT_ROOT of your virtual host"
echo "- test your new instance"
echo "- reconfigure your liJappixPlugin instance"
echo "- delete the old instance -after intensive tests-"
echo "- git pull regularly your e-venement, to avoid security issues"

echo ""
echo "Migration provided for the community of e-venement, by Libre Informatique."
echo "Any feedback to baptiste.simon@libre-informatique.fr."

if [ -z "$GO" ]; then
  echo ""
  echo 'CAREFUL: this was not for real... run this script adding "go" if everthing seems ok.'
fi
