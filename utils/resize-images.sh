#!/usr/bin/env bash

DIRECTORY=$1

if [[ -n "$DIRECTORY" ]] && [ -d "$DIRECTORY" ]; then

    OUTPUT=${DIRECTORY%%/}/output
    echo "Files to resize:"
    echo $(find ${DIRECTORY%%/}/ -maxdepth 1 -iname '*.jpg' -print)
    echo "To output folder:"
    echo $OUTPUT
    mkdir -p $OUTPUT

    find ${DIRECTORY%%/}/ -maxdepth 1 -iname '*.jpg' -print0 | xargs -0 -r mogrify -path $OUTPUT -format jpg -resize x1200

else
    echo "Given directory was not given or does not exist."
fi
