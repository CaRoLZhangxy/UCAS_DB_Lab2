-- 记录用户数据
create table users
(
    u_user_id char(18) primary key,
    u_user_name varchar(20) not null,
    u_pnumber char(11) unique,
    u_credit_card char(16),
    u_account_id varchar(20) not null,
    u_admin integer not null DEFAULT 0, -- 标志是否是管理员账户
    u_password varchar(20) not null     -- 账户密码
);

-- 记录列车信息
create table train
(
    t_train_id varchar(20) primary key,
    t_type char(1) not null
);

-- 记录每个车站的信息
create table stationlist
(
    sl_station_id integer primary key,
    sl_station_name varchar(20) not null,
    sl_city_name varchar(20) not null
);

-- 记录每个车站的经过车次
create table passstation
(
    ps_train_id varchar(20) not null,
    ps_station_id integer not null,
    ps_in_time time,
    ps_out_time time,

    primary key (ps_station_id, ps_train_id),
    foreign key (ps_station_id) references stationlist(sl_station_id),
    foreign key (ps_train_id) references train(t_train_id)
);

-- 记录每趟列车每个区间的信息
create table sectioninfo
(
    -- 主键：列车id，区间id
    si_train_id varchar(20) not null,
    si_section_id integer not null,
    -- 主要区间信息：开始车站id，结束车站id，出发和到达时间
    si_sstation integer not null,
    si_estation integer not null,
    si_sstaytime integer not null,
    si_stime time,
    si_etime time,
    -- 其他区间信息
    si_sell_ticket integer,	        -- 是否卖到到达站的票
    si_duration_time integer,
    si_mileage decimal(5,1),
    si_price_YZ decimal(5,1),
    si_price_RZ decimal(5,1),
    si_price_YWS decimal(5,1),
    si_price_YWZ decimal(5,1),
    si_price_YWX decimal(5,1),
    si_price_RWS decimal(5,1),
    si_price_RWX decimal(5,1),

    primary key (si_train_id, si_section_id),
    foreign key (si_train_id) references train(t_train_id) on delete cascade,
    foreign key (si_sstation) references stationlist(sl_station_id),
    foreign key (si_estation) references stationlist(sl_station_id)
);

-- 记录每趟列车每个区间每天的余票
create table sectionticket
(
    -- 主键：列车id，区间id，日期
    st_train_id varchar(20) not null,
    st_section_id integer not null,
    st_train_date date not null,
    st_sdate date ,
    st_edate date,
    -- 余票信息
    st_tnum_YZ integer,
    st_tnum_RZ integer,
    st_tnum_YWS integer,
    st_tnum_YWZ integer,
    st_tnum_YWX integer,
    st_tnum_RWS integer,
    st_tnum_RWX integer,

    primary key (st_train_id, st_section_id, st_train_date),
    foreign key (st_train_id, st_section_id) references sectioninfo(si_train_id, si_section_id) on delete cascade
);

-- 记录订单信息
create table orders
(
    o_order_id integer primary key,
    o_user_id char(18) not null,
    o_date date not null,
    o_train_id varchar(20) not null,
    o_sstation integer not null,        -- 出发站
    o_estation integer not null,        -- 到达站
    o_price decimal(5, 1) not null,
    o_seat_type integer not null,
    -- 硬座0 软座1 硬卧上2 中3 下4 软卧上5 下6
    -- o_seat_type enum('YZ', 'RZ', 'YWS', 'YWZ', 'YWX', 'RWS', 'RWX'),
    o_status integer not null,
    -- 0 for not paid, 1 for paid, -1 for canceled
    -- o_status enum('unpaid', 'paid', 'cancled')
    o_another_id integer not null default 0,
    foreign key (o_sstation) references stationlist(sl_station_id),
    foreign key (o_estation) references stationlist(sl_station_id),
    foreign key (o_user_id) references users(u_user_id),
    foreign key (o_train_id) references train(t_train_id)
);
