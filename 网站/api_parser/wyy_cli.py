#!/usr/bin/env python3
import sys
import json
import urllib.parse
from hashlib import md5
from random import randrange
import requests
from cryptography.hazmat.primitives import padding
from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes

def HexDigest(data):
    return "".join([hex(d)[2:].zfill(2) for d in data])

def HashDigest(text):
    HASH = md5(text.encode("utf-8"))
    return HASH.digest()

def HashHexDigest(text):
    return HexDigest(HashDigest(text))

def parse_cookie(text):
    cookie_ = [item.strip().split('=', 1) for item in text.strip().split(';') if item]
    cookie_ = {k.strip(): v.strip() for k, v in cookie_}
    return cookie_

def read_cookie():
    return "MUSIC_U=;os=pc;appver=8.9.75;"

def post(url, params, cookie):
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Safari/537.36 Chrome/91.0.4472.164 NeteaseMusicDesktop/2.10.2.200154',
        'Referer': '',
    }
    cookies = {"os": "pc", "appver": "", "osver": "", "deviceId": "pyncm!"}
    cookies.update(cookie)
    response = requests.post(url, headers=headers, cookies=cookies, data={"params": params}, timeout=15)
    return response.text

def extract_id(url):
    if '163cn.tv' in url:
        response = requests.get(url, allow_redirects=False, timeout=10)
        url = response.headers.get('Location', url)
    if 'music.163.com' in url:
        index = url.find('id=') + 3
        sid = url[index:].split('&')[0]
        return sid
    return url

def url_v1(song_id, level, cookies):
    api_url = "https://interface3.music.163.com/eapi/song/enhance/player/url/v1"
    AES_KEY = b"e82ckenh8dichen8"
    config = {
        "os": "pc", "appver": "", "osver": "", "deviceId": "pyncm!",
        "requestId": str(randrange(20000000, 30000000))
    }
    payload = {'ids': [int(song_id)], 'level': level, 'encodeType': 'flac',
               'header': json.dumps(config)}
    url2 = urllib.parse.urlparse(api_url).path.replace("/eapi/", "/api/")
    digest = HashHexDigest(f"nobody{url2}use{json.dumps(payload)}md5forencrypt")
    params_str = f"{url2}-36cd479b6b5-{json.dumps(payload)}-36cd479b6b5-{digest}"
    padder = padding.PKCS7(algorithms.AES(AES_KEY).block_size).padder()
    padded_data = padder.update(params_str.encode()) + padder.finalize()
    cipher = Cipher(algorithms.AES(AES_KEY), modes.ECB())
    encryptor = cipher.encryptor()
    enc = encryptor.update(padded_data) + encryptor.finalize()
    params_enc = HexDigest(enc)
    response = post(api_url, params_enc, cookies)
    return json.loads(response)

def name_v1(song_id):
    urls = "https://interface3.music.163.com/api/v3/song/detail"
    data = {'c': json.dumps([{"id": int(song_id), "v": 0}])}
    response = requests.post(url=urls, data=data, timeout=10)
    return response.json()

def main():
    if len(sys.argv) < 2:
        output(400, '请提供url参数')

    url = sys.argv[1]

    try:
        song_id = extract_id(url)
        cookies = parse_cookie(read_cookie())
        url_data = url_v1(song_id, 'lossless', cookies)

        if not url_data or 'data' not in url_data or not url_data['data']:
            output(400, '未找到有效内容')

        song_info = url_data['data'][0]
        if not song_info.get('url'):
            output(400, '无法获取歌曲地址')

        name_data = name_v1(song_info['id'])

        result = {
            'title': '',
            'cover': '',
            'url': song_info['url'].replace('http://', 'https://', 1),
        }

        if name_data and name_data.get('songs'):
            song = name_data['songs'][0]
            result['title'] = song.get('name', '')
            result['cover'] = song.get('al', {}).get('picUrl', '')

        output(200, '解析成功', result)

    except Exception as e:
        output(500, str(e))

def output(code, msg, data=None):
    print(json.dumps({'code': code, 'msg': msg, 'data': data or {}}))
    sys.exit(0)

if __name__ == '__main__':
    main()
