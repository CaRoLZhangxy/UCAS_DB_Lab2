# UCAS Database Lab2

## 目录结构

| 目录 | 用途 |
| :--: | :--: |
| data | 处理后的数据 |
| php | php文件 |
| report | 实验报告 |
| sql | sql语句 |
| train-2016-10 | 原始数据 |
| data_generate.py | 数据生成 |
| data_process.py | 数据处理 |

## 小组成员

王紫芮 张翔雨 吴俊亮

## 创建数据库

使用[77qiqi/ucas-dbms-hw-2020 (docker.com)](https://hub.docker.com/r/77qiqi/ucas-dbms-hw-2020)提供的docker，在镜像中安装`postgresql`并启动，执行以下命令：

```shell
$ sudo apt-get install postgresql
$ sudo service postgresql start
```

执行psql进入数据库并创建一个名为lab2的数据库

```shell
$ psql
dbms=# create database lab2;
CREATE DATABASE
dbms=# \q
```

进入lab2数据库

```sh
psql -d lab2
```

创建名为postgres的用户，执行以下命令：

```sh
ALTER USER postgres WITH PASSWORD 'dbms';
```

退出数据库，编辑目录下的文件：`/etc/postgresql/10/main/pg_hba.conf`，添加如下内容：

```
      -> # TYPE  DATABASE        USER            ADDRESS                 METHOD
      -> local   all             postgres                                    md5
```

若文件中有如下内容，即存在名为postgres的管理员名，修改postgres为其他名字：

![image](report/pic/readme_pic1.png)

进入数据库

```sh
$ psql -d lab2 -U postgres
```

拷贝sql/create_table.sql中的内容执行，创建数据库。

将数据文件拷贝进入镜像，拷贝数据进入数据库

```c
copy train from 'data/train.csv' with (format csv, delimiter ',');
copy users from 'data/users.csv' with (format csv, delimiter ',');
copy stationlist from 'data/stationlist.csv' with (format csv, delimiter ',');
copy passstation from 'data/passstation.csv' with (format csv, delimiter ',');
copy sectioninfo from 'data/sectioninfo.csv' with (format csv, delimiter ',');
copy sectionticket from 'data/sectionticket.csv' with (format csv, delimiter ',');
```

重启postgresql，使用命令 `sudo service postgresql restart`

启动apache2，使用命令`sudo service apache2 start`

## 访问网页

将所有网页文件拷贝进入镜像的/var/www/html中

可以通过访问http://localhost:8080/welcome.php进入主页，体验使用。