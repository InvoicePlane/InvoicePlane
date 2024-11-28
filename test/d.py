#!/usr/local/bin/python3


import json
import sys
import datetime

# connect to mysql
try:
    import pymysql
except:
    #check for pymysql module
    sys.stderr.write("Error: Module pymysql not installed!")
    sys.exit(-13)

try:
    con = pymysql.connect(host='localhost',
    user='user', password='password', database='database',  charset='utf8')
    curs = con.cursor(pymysql.cursors.DictCursor)
except pymysql.err.Error as error:
    sys.stderr.write("Error with trans db connection: {0}".format(str(error)))
    sys.exit(2)

#print("cursor:",curs)
sql = ('SELECT client_date_created, client_date_modified, client_name, '
    'client_address_1, client_city, client_zip, client_country, '
    'client_phone, client_email, client_gender, client_active FROM ip_clients')
#result = curs.fetchone()
curs.execute(sql)
rows = curs.fetchall()
for r in rows:                
    print("r: " , r)

exit (1);

sql = ('INSERT INTO ip_clients (client_date_created, client_date_modified, client_name, '
    'client_address_1, client_city, client_zip, client_country, '
    'client_phone, client_email, client_gender, client_active) VALUES '
    ' (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)'
)
now = datetime.datetime(2019, 5, 5)

with open('data.json') as f:
    data = json.load(f)
    for item in data:
        val = (now.strftime('%Y-%m-%d %H:%M:%S'), now.strftime('%Y-%m-%d %H:%M:%S'), item['name'],
            item['address'], item['city'], item['postalZip'], 'DE',
            item['phone'], item['email'], 1, 1)
        print(sql)
        print(val)
        curs.execute(sql, val)

