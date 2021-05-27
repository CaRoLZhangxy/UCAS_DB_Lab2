select
    first_section.si_train_id as train,
    start_sl.sl_station_name as sstation,
    end_sl.sl_station_name as estation,
    last_section.si_etime as etime,
    min(all_st.st_tnum_YZ) as YZ,
    min(all_st.st_tnum_RZ) as RZ,
    min(all_st.st_tnum_YWS) as YWS,
    min(all_st.st_tnum_YWZ) as YWZ,
    min(all_st.st_tnum_YWX) as YWX,
    min(all_st.st_tnum_RWS) as RWS,
    min(all_st.st_tnum_RWX) as RWX,
    sum(all_section.si_price_YZ) as price_YZ,
    sum(all_section.si_price_RZ) as price_RZ,
    sum(all_section.si_price_YWS) as price_YWS,
    sum(all_section.si_price_YWZ) as price_YWZ,
    sum(all_section.si_price_YWX) as price_YWX,
    sum(all_section.si_price_RWS) as price_RWS,
    sum(all_section.si_price_RWX)  as price_RWX,  
    (case 
    when min(all_st.st_tnum_YZ) <> 0 then  sum(all_section.si_price_YZ)
    when min(all_st.st_tnum_RZ) <> 0 then  sum(all_section.si_price_RZ)
    when min(all_st.st_tnum_YWS) <> 0 then  sum(all_section.si_price_YWS)
    when min(all_st.st_tnum_YWZ) <> 0 then  sum(all_section.si_price_YWZ)
    when min(all_st.st_tnum_YWX) <> 0 then  sum(all_section.si_price_YWX)
    when min(all_st.st_tnum_RWS) <> 0 then  sum(all_section.si_price_RWS)
    when min(all_st.st_tnum_RWX) <> 0 then  sum(all_section.si_price_RWX)
    else 0
    end) as price,
    (sum(all_section.si_duration_time) - first_section.si_sstaytime) as duration_time,
    first_section.si_stime as start_time
from
    (
        select
            sl_station_id
        from 
            stationlist
        where
            sl_city_name = '$start_city'
    ) as start_station, -- 起点站
    (
        select
            sl_station_id
        from 
            stationlist
        where
            sl_city_name = '$end_city'
    ) as end_station,   -- 终点站
    sectioninfo as first_section,
    sectioninfo as last_section,
    sectioninfo as all_section,
    sectionticket as first_st,
    sectionticket as all_st,
    stationlist as start_sl,
    stationlist as end_sl
where
    first_section.si_train_id = last_section.si_train_id
    and first_section.si_sstation = start_station.sl_station_id
    and last_section.si_estation = end_station.sl_station_id
    and last_section.si_sell_ticket = 0
    and first_section.si_section_id <= last_section.si_section_id   -- 匹配起点终点站
    and first_section.si_stime > '$go_time'                           -- 检查出发时间
    and all_section.si_train_id = first_section.si_train_id
    and all_section.si_section_id >= first_section.si_section_id
    and all_section.si_section_id <= last_section.si_section_id     -- 所有区间信息
    and first_st.st_train_id = first_section.si_train_id
    and first_st.st_section_id = first_section.si_section_id
    and first_st.st_sdate = '$go_date'                                -- 匹配日期
    and all_st.st_train_id = first_section.si_train_id
    and all_st.st_section_id = all_section.si_section_id        
    and all_st.st_train_date = first_st.st_train_date               -- 所有区间余票
    and start_sl.sl_station_id = first_section.si_sstation
    and end_sl.sl_station_id = last_section.si_estation
group by
    first_section.si_train_id,
    first_section.si_sstaytime,
    first_section.si_stime,
    last_section.si_etime,
    start_sl.sl_station_name,
    end_sl.sl_station_name
having
    min(all_st.st_tnum_YZ) + min(all_st.st_tnum_RZ) + min(all_st.st_tnum_YWS) + min(all_st.st_tnum_YWZ) + 
    min(all_st.st_tnum_YWX) + min(all_st.st_tnum_RWS) + min(all_st.st_tnum_RWX) <> 0
order by
    price,
    duration_time,
    start_time
limit
    10;
