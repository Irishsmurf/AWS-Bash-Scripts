import boto.dynamodb2
from boto.dynamodb2.fields import HashKey, RangeKey, GlobalAllIndex
from boto.dynamodb2.items import Item
from boto.dynamodb2.table import Table
import boto.sqs as sqs
import json
import uuid

class bcolors:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'

QUEUE_URL = "https://sqs.eu-west-1.amazonaws.com/AccountID/UUID_Gen"
QUEUE_NAME = "UUID_Gen"
REGION = "eu-west-1"

conn = sqs.connect_to_region(REGION)
queue = conn.get_queue(QUEUE_NAME)
covers = conn.get_queue("Queue")
albums = Table("sometable", connection=boto.dynamodb2.connect_to_region(REGION))

while(True):
    try:
        exists = False
        outbound = {}
        messages = queue.get_messages(num_messages=10, wait_time_seconds=20)
        for message in messages:
            raw = message.get_body()
            json_raw = json.loads(raw)
            
            #See if this exists in the DB:
            result = albums.query_2(artist__eq=json_raw["artist"], album__eq=json_raw["album"], index="artist-album-index")
            
            for album in result:
                if album['mbid']:
                    exists = True;
                    print bcolors.FAIL+"Exists: ["+album['mbid']+"] "+album['artist']+" - "+album['album']+bcolors.ENDC
                    #album.delete()
            if not exists:
                #Doesn't exist
                mbid = uuid.uuid4()
                json_raw['mbid'] = str(mbid)
                json_raw['plays'] = json_raw['playcount']
                del json_raw['playcount']
                print bcolors.OKGREEN+"Adding ["+str(mbid)+"] "+json_raw["artist"]+" - "+json_raw["album"]+bcolors.ENDC
                album = Item(albums, data=json_raw)
                album['user'] = set([json_raw['user']])
                outbound['mbid'] = str(mbid)
                outbound['cover_url'] = json_raw['url']
                outbound['artist'] = json_raw['artist']
                outbound['album'] = json_raw['album']

                json_data = json.dumps(outbound)
                conn.send_message(covers, json_data)
                album.save()
            queue.delete_message(message)
    except Exception, e:
        print e
