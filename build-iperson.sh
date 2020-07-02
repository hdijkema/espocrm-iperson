#!/bin/bash
# vi: set sw=4 ts=4:

BE='./build-ext.sh'
if [ -e build-ext.sh ]; then
    cp build-ext.sh build-ext.dist.sh
    chmod 755 build-ext.dist.sh
else
    BE='./build-ext-dist.sh'
fi

CMD=$1;
VERSION="0.2.7"
EXT="iperson-extension"
NAME="Field PersonName with initials - IpersonName"
DESCRIPTION="Creates a new IpersonName Field type that can be used instead of PersonName, which also has initials"
MODULE=IPerson

$BE "$CMD" "$VERSION" "$EXT" "$NAME" "$DESCRIPTION" "$MODULE"

