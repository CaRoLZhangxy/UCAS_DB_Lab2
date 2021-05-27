select 
    station.sl_station_name, 
    station.ps_in_time, 
    station.ps_out_time,
    info.price_YZ,
    info.price_RZ,
    info.price_YWS,
    info.price_YWZ,
    info.price_YWX,
    info.price_RWS,
    info.price_RWX,
    info.YZ,
    info.RZ,
    info.YWS,
    info.YWZ,
    info.YWX,
    info.RWS,
    info.RWX
from
    (
        select 
            ps_station_id,
            sl_station_name,
            ps_in_time, 
            ps_out_time
        from
            stationlist,
            passstation
        where
            ps_train_id = '$trainid'
            and ps_station_id = sl_station_id
    ) as station
    left outer join
    (
        select
            cur.si_section_id as section_id,
            cur.si_estation as estation,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YZ) else 0 end) as price_YZ,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_RZ) else 0 end) as price_RZ,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YWS) else 0 end) as price_YWS,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YWZ) else 0 end) as price_YWZ,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YWX) else 0 end) as price_YWX,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_RWS) else 0 end) as price_RWS,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_RWX) else 0 end) as price_RWX,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_YZ) else 0 end) as YZ,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_RZ) else 0 end) as RZ,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_YWS) else 0 end) as YWS,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_YWZ) else 0 end) as YWZ,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_YWX) else 0 end) as YWX,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_RWS) else 0 end) as RWS,
            (case when cur.si_sell_ticket = 0 then min(st_tnum_RWX) else 0 end) as RWX
        from
            sectioninfo as cur,
            sectioninfo as total,
            sectionticket
        where
            cur.si_train_id = '$trainid'
            and total.si_train_id = cur.si_train_id
            and total.si_section_id <= cur.si_section_id
            and st_train_id = cur.si_train_id
            and st_section_id = total.si_section_id
            and st_sdate = '$date'
        group by
            cur.si_section_id,
            cur.si_estation,
            cur.si_sell_ticket
    ) as info
on
    station.ps_station_id = info.estation
order by
    (case when info.section_id is null then 0 else info.section_id end);
