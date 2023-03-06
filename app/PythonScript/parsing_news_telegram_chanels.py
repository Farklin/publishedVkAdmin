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
import os
from dotenv import load_dotenv
import requests
import re 
import datetime

path = "/home/f/firetecr/firetecr.beget.tech/public_html/"
env = path + ".env" 
path_media = path + "public/images/"


#удаление всех файлов в час ночи
content = os.listdir(path_media)
if datetime.datetime.now().hour == 1:
    for f in content:
        os.remove(path_media + f)

#удаление дублей файлов 
for f in content:
    if re.search(r"[(][2-9][)]", f):   
        os.remove(path_media + f)
    if re.search(r"[(][0-9][0-9][)]", f):   
        os.remove(path_media + f)


load_dotenv(env)

api_id = os.getenv('API_TELEGRAM_ID')
api_hash = os.getenv('API_TELEGRAM_HASH')



client = TelegramClient(path + 'session_name', api_id, api_hash)
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

# функция получения данных с сервера по ключу json 
def parsinJosn(url, key):
    mas = []
    for word in requests.get(url).json():
        mas.append(word[key])
    
    return mas

def callback(current, total):
    print('Downloaded', current, 'out of', total,
          'bytes: {:.2%}'.format(current / total))

# posts = parsinJosn("http://firetecr.beget.tech/api/posts", 'description')

key_word = parsinJosn("http://firetecr.beget.tech/api/words", 'word')

def parsingChanel(title): 
 
    # shutil.rmtree(path_media) 
    # os.mkdir(path_media)

    chanel = client(GetFullChannelRequest(channel=title))
    print(title + '\n')
    messages = client.get_messages(chanel.full_chat, limit=40)

    grouped_ids = [] 

    def mediaFromMessage(messages, grouped_id):
        medias = [] 
        for ms in messages: 
            if ms.grouped_id == grouped_id:
                medias.append(ms.download_media(path_media))

        return medias


    

    result = []

    for ms in messages: 

        el = dict()
        el['chanel_name'] = title 
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
                    el['media'].append(ms.download_media(path_media, progress_callback=callback))

            if el['message'] != '': 
                result.append(el)

    return result




titles = parsinJosn("http://firetecr.beget.tech/api/telegram_chanels", 'name')

result_all = [] 
for title in titles:
    try: 
        result_all  += parsingChanel(title)
    except:
        pass

with open(path + 'public/' 'telegram_chanels.json', 'w', encoding='utf-8') as f:
    json.dump(result_all, f, ensure_ascii=False, indent=4)