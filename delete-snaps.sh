#!/bin/bash

CUTOFF_DATE='2012-01-01'

declare -a TYPE=(`ec2dsnap | awk '{print $1}'`)
declare -a SNAP_IDS=(`ec2dsnap | awk '{print $2}'`)
declare -a DATES=(`ec2dsnap | awk '{print $5}' | awk -F "T" '{print $1}'` )

echo "deleting snapshots created before " $CUTOFF_DATE ":"$'\n'

for i in  $(seq 0 $(( ${#SNAP_IDS[*]} - 1 )))
do
        if [ "${TYPE[$i]}" == "SNAPSHOT" ]
        then
                echo "Selecting" ${SNAP_IDS[$i]}", Date: " ${DATES[$i]}

                if [ "${DATES[$i]}" \< "$CUTOFF_DATE" ]
                then
                        echo "deleting snapshot $i:" ${SNAP_IDS[$i]} ", which was created on" ${DATES[$i]}
                        ec2delsnap ${SNAP_IDS[$i]}
                fi
        fi
done

exit
