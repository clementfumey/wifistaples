#!/bin/bash
rm /var/www/wifistaples/result/*
inotifywait --monitor --event create,close_write --format '%f %e' --exclude ".*\.json" ~/wifistaples/result |
while read file event; do
    if [ "$event" == "CREATE" -a "$file" != "config" ]; then
	    echo "un fichier a été créé"
        php -f ~/wifistaples/csvToJson.php $file
    elif [ "$event" == "CLOSE_WRITE,CLOSE" -a "$file" == "config" ]; then
	    echo "config a été modifié"
        php -f ~/wifistaples/createIndex.php
    elif [ "$event" == "CLOSE_WRITE,CLOSE" ]; then
	    echo "le fichier $file a été modifié"
        php -f ~/wifistaples/csvToJson.php $file
    else
        #do something for delete
        echo "On s'en fout : $event $file"
    fi
done

