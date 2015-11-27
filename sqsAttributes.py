#!/usr/bin/env python
import pprint
import boto.sqs
import md5

from time import gmtime, strftime
from boto.sqs.message import Message

conn = boto.sqs.connect_to_region(
        "eu-west-1")


q = conn.get_queue("thisIsATest")
m = Message()
m.message_attributes = {
    "AttributeA": {
        "data_type": "String",
        "string_value": strftime("%H:%M:%S", gmtime())
    },
    "AttributeB": {
        "data_type": "Number",
        "string_value": "12"
    },
    "AttributeC": {
        "data_type": "String",
        "string_value": "lol"
    }
}
m.set_body('This is a Message')
pp = pprint.PrettyPrinter(indent=4)
q.write(m)

print(m.id)
print(m.md5)
print(" ")
m = q.get_messages(message_attributes=["*"])
print(m[0].md5)
pp.pprint(m[0].get_body())
pp.pprint(m[0].message_attributes)
q.delete_message(m[0])

