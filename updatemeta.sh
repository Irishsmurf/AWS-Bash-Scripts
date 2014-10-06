#!/bin/bash

EXPIRES_DATE="2014-08-21 12:30"
CONTENT_TYPE="image/jpeg"
BUCKET="paddez"
PREFIX=""

echo "Updating Meta-data for -"

for OBJECT in $(aws s3 ls s3://$BUCKET/$PREFIX | awk '{print ($NF)}' | grep -v '/')
do
    echo "s3://$BUCKET$PREFIX$OUTPUT"
    aws s3api copy-object --expires "$EXPIRES_DATE" --content-type "$CONTENT_TYPE" --copy-source "$BUCKET/$PREFIX$OBJECT" --bucket $BUCKET --key $OBJECT --metadata-directive REPLACE
done

