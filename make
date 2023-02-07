#!/bin/zsh

while read page
do xml=$page".xml"
    html=$page".html"

    if [[ ! -e $xml ]]
    then echo "$xml n'existe pas."
        continue
    fi

    if xsltproc xml2html_SB.xsl $xml > $html
    then echo "$xml -> $html : OK"
    else echo "$xml -> $html : échec"
	fi
done < listepages
