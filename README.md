# 2THApi

classes:
http://localhost/2THPlatform/api/v1/platform/
http://localhost/2THPlatform/api/v1/report/
http://localhost/2THPlatform/api/v1/user/

methods:
http://localhost/2THPlatform/api/v1/report/send/
http://localhost/2THPlatform/api/v1/report/get/
http://localhost/2THPlatform/api/v1/report/update/
http://localhost/2THPlatform/api/v1/user/login/
http://localhost/2THPlatform/api/v1/user/get/
http://localhost/2THPlatform/api/v1/user/delete/
http://localhost/2THPlatform/api/v1/user/update/

# Sending data to API 

* Location
MAC:1116144D4DFB
LAT:-21.0059731
LON:26.77222188

http://localhost/api/v1/report/send/?company=2TH&password=123&user=giovana&mac=1A2B3C4D5F6F&lat=21.21&lon=12.21&tel=12313131231&model=ASUSXB00&manufacturer=ASUS

* Login
/login/?company=2TH&user=pedro&password=testepassword
/login/?company=Company&user=chris&password=passtest

* Update

# Database

company 

/ reports 
id - autoincrement
mac - varchar(12)
name - varchar(64)
tag - varchar(32)
groups - varchar(128)
locations - JSON 
'''
{
    {
      "date": "20-20-20",
      "lat": "20",
      "lon": "-20"
    },
    {
      "date": "10-10-15",
      "lat": "15",
      "lon": "-15"
    }
}
'''
telephone - varchar(32)
model - varchar(32)
manufacturer - varchar(32)
downloaded - int
uploaded - int

/ users
id - autoincrement
name - varchar(16)
theme - varchar(8)
type - varchar(8)
password - varchar(64)

# Db users

user: login
pass: 123
permissions: 
- table 'users' in 'id', 'name', 'theme', 'type'

user: read
pass: 123
permissions: 
- read acess to all (except for user 'password')

user: connector
pass: 123
permissions:
- read acess to all reports
- insert and update acess to all reports

user: root (default root user of mysql)

# Users

-/ Updating user information:
http://localhost/2THPlatform/api/v1/user/update/?company=2TH&name=pedro&password=testepassword&new-name=alberto&new-type=admin&new-password=teste&action=chris&new-theme=dark