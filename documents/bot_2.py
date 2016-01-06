import requests
import string
import random

def post_some_data(c, url, post_data, header_data) :
    while 1 :
        try :
            c.post(url, data = post_data, headers = header_data)
            break
        except :
            print "error, retrying..."

def get_some_data(c, url, header_data) :
    while 1 :
        try :
            c.get(url, headers = header_data)
            break
        except :
            print "error, retrying..."

def id_generator(size=10, chars=string.ascii_uppercase + string.digits):
    return ''.join(random.choice(chars) for _ in range(size))
    


header_data = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36"}

def buy_the_land():
    with requests.Session() as c :
        get_some_data(c, "http://landgrab.xyz/index.php/world/6", header_data)
        
        rpassword = "12345678"
        rusername = id_generator()
        y_coord = 28
        x_coord = -84
    
        post_data = {
                        "world_key": "6",
                        "username": rusername,
                        "password": rpassword,
                        "confirm": rpassword,
                    }
    
        post_some_data(c, "http://landgrab.xyz/index.php/user/register", post_data, header_data)
    
        post_data = {
                        "world_key": "6",
                        "username": rusername,
                        "password": rpassword,
                    }
        post_some_data(c, "http://landgrab.xyz/index.php/user/login", post_data, header_data)
    
        post_data = {
                        "form_type_input": "buy",
                        "world_key_input": "6",
                        "coord_slug_input": str(y_coord) + "," + str(x_coord),
                        "lng_input": str(x_coord),
                        "lat_input": str(y_coord),
                        "land_name": "",
                        "price":"0",
                        "content":""
                    }
        post_some_data(c, "http://landgrab.xyz/land_form", post_data, header_data)

def rebuy_the_land():
    with requests.Session() as c :
        get_some_data(c, "http://landgrab.xyz/index.php/world/6", header_data)
        
        rpassword = "" #main account password
        rusername = "" #main account username
        y_coord = 28
        x_coord = -84
    
        post_data = {
                        "world_key": "6",
                        "username": rusername,
                        "password": rpassword,
                    }
        post_some_data(c, "http://landgrab.xyz/index.php/user/login", post_data, header_data)
    
        post_data = {
                        "form_type_input": "buy",
                        "world_key_input": "6",
                        "coord_slug_input": str(y_coord) + "," + str(x_coord),
                        "lng_input": str(x_coord),
                        "lat_input": str(y_coord),
                        "land_name": "",
                        "price":"999999",
                        "content":""
                    }
        post_some_data(c, "http://landgrab.xyz/land_form", post_data, header_data)
while 1:
    buy_the_land()
    rebuy_the_land()
    print("made dollars")