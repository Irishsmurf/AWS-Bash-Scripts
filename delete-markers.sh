## Delete the delete markers in a specified bucket
#!/bin/bash

aws s3api list-object-versions --bucket $1 --output json --query 'DeleteMarkers[?IsLatest==`true`]' --region us-east-1 | \
jq -r '.[] | "--key '\''" + .[0] + "'\'' --version-id " + .[1]' |\
xargs -p -L1 aws s3api delete-object --bucket $1
