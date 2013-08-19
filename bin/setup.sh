#!/usr/bin/env bash

CALLEDPATH=`dirname $0`

# Convert to an absolute path if necessary
case "$CALLEDPATH" in
  .*)
    CALLEDPATH="$PWD/$CALLEDPATH"
    ;;
esac

if [ ! -f "$CALLEDPATH/setup.conf" ]; then
  echo
  echo "Missing configuration file. Please copy $CALLEDPATH/setup.conf.txt to $CALLEDPATH/setup.conf and edit it."
  exit 1
fi

source "$CALLEDPATH/setup.conf"
cp $CIVIROOT/xml/schema/Schema.xml $CIVIROOT/xml/schema/Schema.xml.backup

cp $EXTROOT/xml/schema/Schema.xml $CIVIROOT/xml/schema/Schema.xml
if [ ! -e "$CIVIROOT/xml/schema/Booking" ] ; then
  ln -s $EXTROOT/xml/schema/Booking $CIVIROOT/xml/schema/Booking
fi
cd $CIVIROOT/xml
php GenCode.php
# (There may be extra arguments to pass into GenCode.php; not sure)

cp -f $CIVIROOT/CRM/Booking/DAO/* $EXTROOT/CRM/Booking/DAO/
mv $CIVIROOT/xml/schema/Schema.xml.backup $CIVIROOT/xml/schema/Schema.xml

unlink $CIVIROOT/xml/schema/Booking
rm -rf $CIVIROOT/CRM/Booking
