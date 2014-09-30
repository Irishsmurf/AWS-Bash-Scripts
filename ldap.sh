#!/bin/bash
#Cat a file of Student ID's into the script.
#Will traverse each line and query them
while read line
        do

                ldapsearch -x -h atlas.dcu.ie -b o=dcu cn="$line" | grep "mail:" | sed s/"mail:"/"$line - "/g   
        done
