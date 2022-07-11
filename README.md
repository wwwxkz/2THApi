# Install

### Automatic
Use install.sh for setting up db, tables, and users
```
chmod +x install.sh
./install -c 
./install -a
```
For help you can use
```
./install -h
```
For instance
```
Server config has to be made manually
Database users created by the script have total access to your database, it is highly recommended that you setup users manually 
Users here have the same name and password, but more permissions than the ones described below
```

### Manual

Database

In order to setup, you need to set these tables, and users. You can change the password or better, create a new user to any task, and even create new taks and read just users, or read just reports, it is up to you and recommended for security reassons

Database -> company 

Table -> reports 
```
id - autoincrement
mac - varchar(12)
name - varchar(64)
tag - varchar(32)
groups - varchar(128)
locations - JSON 
telephone - varchar(32)
model - varchar(32)
manufacturer - varchar(32)
downloaded - int
uploaded - int
```
Table -> users
```
id - autoincrement
user - varchar(16)
theme - varchar(8)
type - varchar(8)
password - varchar(64)
```

Database users 
User -> login
```
- Password -> 123
- Permissions
  - table 'users' in the 'id' specified, 'name', 'theme', 'type'
```

User -> read
```
- Password -> 123
- Permissions
  - read acess to all (except for user 'password')
```

User -> connector
```
- Password -> 123
- Permissions
  - read acess to all reports
  - insert and update acess to all reports
```

User -> root 
```
- Password -> (in my case none, it depends on your instalation)
- Permissions
  - default root user of mysql, default permission
```


# API

Classes
```
http://localhost/2THPlatform/api/v1/platform/
http://localhost/2THPlatform/api/v1/report/
http://localhost/2THPlatform/api/v1/user/
```

Methods
```
http://localhost/2THPlatform/api/v1/report/send/
http://localhost/2THPlatform/api/v1/report/get/
http://localhost/2THPlatform/api/v1/report/update/
http://localhost/2THPlatform/api/v1/user/login/
http://localhost/2THPlatform/api/v1/user/get/
http://localhost/2THPlatform/api/v1/user/delete/
http://localhost/2THPlatform/api/v1/user/update/
```

Sending data to API 
Location
  - MAC:1116144D4DFB
  - LAT:-21.0059731
  - LON:26.77222188
```
http://localhost/api/v1/report/send/?company=2TH&password=123&user=giovana&mac=1A2B3C4D5F6F&lat=21.21&lon=12.21&tel=12313131231&model=ASUSXB00&manufacturer=ASUS
```
Location (Processed data sample)
```
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
```

Login
```
/login/?company=2TH&user=pedro&password=testepassword
/login/?company=Company&user=chris&password=passtest
```
User
```
http://localhost/2THPlatform/api/v1/user/update/?company=2TH&name=pedro&password=testepassword&new-name=alberto&new-type=admin&new-password=teste&action=chris&new-theme=dark
```

### Server config

.env

Get an api key https://developers.google.com/maps/documentation/javascript/get-api-key
Setup inside 2THPlatform root folder
```
$key_map = 'google_maps_api_key';
```

### Acess
Base url
```
http://localhost/2THPlatform
```
If not logged you will be redirected to Login page, there you should use a user you created inside users table, not the users script or you created in mysql. For usability script creates 3 default users
```
- Default guest
   - Name -> guest
   - Password -> 123
   * Type -> read 
```
```
- Default user
   - Name -> user
   - Password -> 123
   * Type -> write
```
```
- Default admin
   - Name -> admin
   - Password -> 123
   * Type -> admin
```   


### This is a submodule of https://github.com/wwwxkz/2THPlatform
