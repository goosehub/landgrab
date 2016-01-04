import requests
 
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
   
 
header_data = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36"}
with requests.Session() as c :
    get_some_data(c, "http://landgrab.xyz/index.php/world/6", header_data)
    post_data = {"world_key": "6",
                 "username":"",   #username
                 "password": ""}  #password
    post_some_data(c, "http://landgrab.xyz/index.php/user/login", post_data, header_data)
    for x in range(89) :
        x_coord = x*4-176
        for y in range(41) :
            y_coord = y*4-84
            post_data = {"form_type_input": "claim",
                         "world_key_input": "6",
                         "coord_slug_input": str(y_coord) + "," + str(x_coord),
                         "lng_input": str(x_coord),
                         "lat_input": str(y_coord),
                         "land_name": "BUNE RAVEN IS A MEME",
                         "price":"0",
                         "content":"BUNE RAVEN IS A MEME"}
            post_some_data(c, "http://landgrab.xyz/land_form", post_data, header_data)
            print str(y_coord) + "," + str(x_coord)