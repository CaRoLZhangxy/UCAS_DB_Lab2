        select o_order_id,o_date,o_train_id,A.sl_station_name as sstation,B.sl_station_name as estation,o_price,o_seat_type,o_status
          from orders, stationlist as A, stationlist as B
          where o_user_id = '$user_id'
          and o_date >= '$outdate' 
          and o_date <= '$indate'
          and a.sl_station_id = o_sstation
          and b.sl_station_id = o_estation;


select 
    station.sl_station_name, 
    station.ps_in_time, 
    station.ps_out_time,
    station.stay_time
from
    (
        select 
            ps_station_id,
            sl_station_name,
            ps_in_time, 
            ps_out_time,
            (case when (ps_out_time - ps_in_time) < interval'0min' then (ps_out_time - ps_in_time + interval'24hour')
            else (ps_out_time - ps_in_time) end) as stay_time
        from
            stationlist,
            passstation
        where
            ps_train_id = '1095'
            and ps_station_id = sl_station_id
    ) as station
    left outer join
    (
        select
            cur.si_section_id as section_id,
            cur.si_estation as estation
        from
            sectioninfo as cur
        where
            cur.si_train_id = '1095'
    ) as info
on
    station.ps_station_id = info.estation
order by
    (case when info.section_id is null then 0 else info.section_id end);
