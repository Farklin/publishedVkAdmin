from telethon import TelegramClient, events, sync
from telethon.tl.functions.messages import GetHistoryRequest
from xmlrpc.client import DateTime
from telethon.sync import TelegramClient
import csv
from telethon.tl.functions.messages import GetDialogsRequest
from telethon.tl.types import InputPeerEmpty
from telethon.tl.functions.messages import GetHistoryRequest
from telethon.tl.types import PeerChannel
import json 
from telethon.tl.functions.channels import GetFullChannelRequest
import glob
import os 
import shutil


api_id = 1984546
api_hash = '05de3877482473e934f58593944edf3a'
path_media = 'public/images/'

client = TelegramClient('session_name', api_id, api_hash)
client.start()

chats = []
last_date = None
chunk_size = 200
groups = []
result = client(GetDialogsRequest(
    offset_date=last_date,
    offset_id=0,
    offset_peer=InputPeerEmpty(),
    limit=chunk_size,
    hash=0
))
chats.extend(result.chats)

def callback(current, total):
    print('Downloaded', current, 'out of', total,
          'bytes: {:.2%}'.format(current / total))




def parsingChanel(title): 
 
    # shutil.rmtree(path_media) 
    # os.mkdir(path_media)

    chanel = client(GetFullChannelRequest(channel=title))
    print(chanel.full_chat)
    messages = client.get_messages(chanel.full_chat, limit=40)

    grouped_ids = [] 

    def mediaFromMessage(messages, grouped_id):
        medias = [] 
        for ms in messages: 
            if ms.grouped_id == grouped_id:
                medias.append(ms.download_media(path_media))

        return medias


    key_word = ['пожар']
    result = []

    for ms in messages: 

        el = dict()
        el['message'] = ''
        el['media'] = [] 
        
        #проверка ключевых слов 
        flag = False 
        for key in key_word:
            if key in ms.message:
                flag = True
                break
        
        if flag:
            
            if ms.grouped_id != None and ms.message != '':
                el['message'] = ms.message
                el['media'] = mediaFromMessage(messages, ms.grouped_id)

            if ms.grouped_id == None and ms.message != '': 
                el['message'] = ms.message
                if ms.media != None:
                    el['media'].append(ms.download_media('images_telegram_chanel/', progress_callback=callback))

            if el['message'] != '': 
                result.append(el)

    with open('public/' + 'telegram_chanels.json', 'w', encoding='utf-8') as f:
        json.dump(result, f, ensure_ascii=False, indent=4)

    

title = "РИА Новости"
parsingChanel(title)