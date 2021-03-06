#!/bin/bash

ERROR=false

for file in `find .`
do
    EXTENSION="${file##*.}"

    if [ "$EXTENSION" == "php" ] || [ "$EXTENSION" == "phtml" ]
    then
        RESULTS=`php -l $file`

        if [ "$RESULTS" != "No syntax errors detected in $file" ]
        then
            echo $RESULTS
			ERROR=true
        fi
    fi
done

if [ "$ERROR" = true ] ; then
    exit 1
else
    exit 0
fi
