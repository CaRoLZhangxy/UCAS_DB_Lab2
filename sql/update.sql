--插入用户条目
insert into users 
values ('$u_user_id','$u_user_name','$u_pnumber','$u_credit_card','$u_account_id',0,'$u_password');
    --例子
    insert into users 
    values ('123123123123123123','阿姨','12312312312','1231231231231231','ayi',1,'123');

--插入订单条目
insert into orders
values ($orderid,'$userid','$date','$trainid',$sstation,$esstation,$price,$seattype,1);
    --例子
    insert into orders
    values (1,'370602200003281617','2021-5-23','1095',798,1873,21.5,0,1);

select sl1.sl_station_id,sl2.sl_station_id
from stationlist as sl1,stationlist as sl2
where sl1.sl_station_name = '北京'
and   sl2.sl_station_name = '上海';

--减少余票
update sectionticket
set st_tnum_YZ = st_tnum_YZ - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_RZ = st_tnum_RZ - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_YWS = st_tnum_YWS - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_YWZ = st_tnum_YWZ - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_YWX = st_tnum_YWX - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_RWS = st_tnum_RWS - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_RWX = st_tnum_RWX - 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

--取消订单
update orders
set o_status = -1
where o_order_id = $orderid;
--恢复余票
update sectionticket
set st_tnum_YZ = st_tnum_YZ + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_RZ = st_tnum_RZ + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_YWS = st_tnum_YWS + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_YWZ = st_tnum_YWZ + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_YWX = st_tnum_YWX + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_RWS = st_tnum_RWS + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);

update sectionticket
set st_tnum_RWX = st_tnum_RWX + 1
from sectioninfo as si1,sectioninfo as si2
where 
    st_train_id = '$train_id' 
    and si1.si_train_id = st_train_id
    and si2.si_train_id = st_train_id
    and si1.si_sstation = $sstation
    and si2.si_estation = $estation
    and st_date = '$date' 
    and (st_section_id >= si1.si_section_id) 
    and (st_section_id <= si2.si_section_id);
