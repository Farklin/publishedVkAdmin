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

path = "/home/f/firetecr/firetecr.beget.tech/public_html/"
env = path + ".env" 
path_media = path + "public/images/"

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


    key_word = [
        'возгорания',
        'возгорание',
        'пожаре',
        'загорания',
        'загорание',
        'пожаров',
        'пожар',
    ]


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




titles = ['Mash', 'Раньше всех. Ну почти.', 'СМИ Россия не Москва', 'Новости Москвы', 'TABOO', 'Baza', 'WarGonzo', 'Поддубный |Z|О|V| edition', 'Медуза — LIVE', 'Военный Осведомитель', 'SHOT', 'КБ', 'Старше Эдды', 'НЕ МОРГЕНШТЕРН', 'NEXTA', 'Ньюсач/Двач', 'Зеркало | Новости', 'Ateo Breaking', 'ТАСС', 'Повёрнутые на Z войне', 'Жесть Белгород', 'Коммерсантъ', 'ANNA-NEWS', 'Super.ru', 'YOBAJUR', 'Дабл Ять', 'Только никому...', 'На Почте', 'Политика Страны', 'Москва 24', 'BBC News | Русская служба', 'Москва сейчас', 'Anton S Live', 'Военный обозреватель', 'Белгород №1', 'Светские хроники', 'Новости', 'concertzaal', 'Блокнот Россия', 'Работаем с Пушкиным!', 'Срочно, Сейчас', 'Мурзилка', 'Лента дня', 'Onliner', 'Крамола', 'Газетчик | Новости Live', 'ЁЖ', 'The Moscow Post', 'The Bell', 'Kun.uz | Новости Узбекистана', 'Типичный Краснодар', 'Что там в Москве?', 'E1.RU | Новости Екатеринбурга', 'СОРОКА | Новости | Сегодня', 'Фонтанка SPB Online', 'TJ', 'ДЖОКЕР | Новости', 'Москва с огоньком', 'The Village', 'Только интересные материалы и никаких новостей', 'Планёрка', 'Санкт-Петербург Life', 'Сетевые Свободы', 'OFNEWS / Новости Околофутбола', 'Что там у немцев?', 'Брюссельский стукач', 'MountShow', 'URA.RU', 'Mash Siberia', 'РИА Новости: США', 'Журналистика', 'Китайская угроза', 'Донбасс решает', 'Страновед', 'Mash на волне', 'Москва • Происшествия • Новости Москвы', 'ЧП Ульяновск', 'Нетипичная Махачкала', 'Лента дна', 'РИА Новости', 'Осторожно, новости', 'Readovka', 'Давыдов.Индекс', 'Вечерний Телеграмъ', 'Varlamov News', 'RT на русском', 'Мир сегодня с "Юрий Подоляка"', 'СИГНАЛ', 'Война История Оружие', 'Рифмы и Панчи', 'Лентач', 'Kotsnews', 'Телеканал Дождь', 'BLACK JOURNAL | Новости Звезд', 'Типичный Донецк', 'Coronavirus Info', 'Новая газета', 'Комсомольская правда: KP.RU', 'Неофициальный Безсонов "Z"', 'Лентач', 'Российская Газета | Новости', 'Оперативные сводки', 'КК', 'РБК', 'aavst2022', 'Антиглянец', 'МотолькоПомоги', 'Mash на Мойке', '112', 'Tengrinews.kz - Новости Казахстана', 'Метро Петербурга', 'Инфобомба', 'Милитарист', 'НТВ', 'Настоящее Время', 'Оперштаб Москвы', 'Новости праVда', 'NEWS.ru | Новости', 'Мерзкий Кокобай', 'Чай з малинавым варэннем', 'True Питер', 'НеМалахов', 'Rogandar NEWs: Новости, факты, события!', 'Подъём', 'Спорт Инсайд | Новости Прогнозы', 'Коронавирус: рейсы в РФ', 'Мой Питер', 'Новости Россия 24/7', 'Компромат ГРУПП', 'ВЕСТИ', 'murders', 'Медиакиллер', 'ИноСМИ', 'IZ.RU', 'ШоуТайм | Свежее Здесь', 'Журнал НОЖ', 'СТРИНГЕР | НОВОСТИ', 'ЧП Беларусь ? Будь в курсе', 'Происшествия и новости Беларуси', 'Signal', 'ИА «Панорама»', 'Inside ? Donetsk', 'IF News', 'Баграмян 26', 'Don Mash', 'vc.ru', 'Коза кричала', 'Вся Корея', 'Политический обозреватель', 'Актуальные новости политики 24/7', 'Mash Iptash', 'ШЭР / Шеринг. Экология. Рациональность', '360tv', 'FreshNews | Новости | Москва', 'Москва', 'Недвижимость инсайды', 'Kub Mash', 'Ni Mash', 'ЧП Краснодар™']

result_all = [] 
for title in titles:
    try: 
        result_all  += parsingChanel(title)
    except:
        pass

with open(path + 'public/' 'telegram_chanels.json', 'w', encoding='utf-8') as f:
    json.dump(result_all, f, ensure_ascii=False, indent=4)