select
    train1_etime,
    train1_train as train1_id,
    start1_sl.sl_station_name as sstation,
    end1_sl.sl_station_name as trans_station1,
    (case when (stime - train1_etime) < interval'0min' then (stime - train1_etime + interval'24hour')
    else (stime - train1_etime) end) as wait_time,
    sdate as train2_sdate,
    train as train2_id,
    stime as train2_stime,
    etime as train2_etime,
    start2_sl.sl_station_name as trans_station2,
    end2_sl.sl_station_name as estation,
    train1_YZ,
    train1_RZ,
    train1_YWS,
    train1_YWZ,
    train1_YWX,
    train1_RWS,
    train1_RWX,
    train1_price_YZ,
    train1_price_RZ,
    train1_price_YWS,
    train1_price_YWZ,
    train1_price_YWX,
    train1_price_RWS,
    train1_price_RWX,
    YZ,
    RZ,
    YWS,
    YWZ,
    YWX,
    RWS,
    RWX,
    price_YZ,
    price_RZ,
    price_YWS,
    price_YWZ,
    price_YWX,
    price_RWS,
    price_RWX,
    (train1_price + price) as price,
    train1_duration_time +
    duration_time +
    (case
    when (extract(hour from stime) * 60 - extract(hour from train1_etime) * 60 +
    extract(minute from stime) - extract(minute from train1_etime)) < 0
    then (extract(hour from stime) * 60 - extract(hour from train1_etime) * 60 +
    extract(minute from stime) - extract(minute from train1_etime) + 24 * 60)
    else (extract(hour from stime) * 60 - extract(hour from train1_etime) * 60 +
    extract(minute from stime) - extract(minute from train1_etime))
    end)
    as duration_time,
    train1_stime as start_time
from
    (
        select
            first_section.si_train_id as train,
            first_st.st_sdate as sdate,
            first_section.si_stime as stime,
            last_section.si_etime as etime,
            first_section.si_sstation as station,
            last_section.si_estation as end_station,
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

            train1.train as train1_train,
            train1.stime as train1_stime,
            train1.etime as train1_etime,
            train1.start_station as train1_start_station,
            train1.station as train1_station,
            train1.edate as train1_edate,          
            train1.YZ as train1_YZ,
            train1.RZ as train1_RZ,
            train1.YWS as train1_YWS,
            train1.YWZ as train1_YWZ,
            train1.YWX as train1_YWX,
            train1.RWS as train1_RWS,
            train1.RWX as train1_RWX,
            train1.price_YZ as train1_price_YZ,
            train1.price_RZ as train1_price_RZ,
            train1.price_YWS as train1_price_YWS,
            train1.price_YWZ as train1_price_YWZ,
            train1.price_YWX as train1_price_YWX,
            train1.price_RWS as train1_price_RWS,
            train1.price_RWX as train1_price_RWX,
            train1.price as train1_price,
            train1.duration_time as train1_duration_time
        from
            (
                select
                    first_section.si_train_id as train,
                    first_section.si_stime as stime,
                    last_section.si_etime as etime,
                    first_section.si_sstation as start_station,
                    last_section.si_estation as station,            
                    last_st.st_edate as edate,          
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
                    (sum(all_section.si_duration_time) - first_section.si_sstaytime) as duration_time
                from
                    (
                        select
                            sl_station_id
                        from 
                            stationlist
                        where
                            sl_city_name = '$start_city'
                    ) as start_station, 
                    sectioninfo as first_section,
                    sectioninfo as last_section,
                    sectioninfo as all_section,
                    sectionticket as first_st,
                    sectionticket as last_st,
                    sectionticket as all_st
                where
                    first_section.si_train_id = last_section.si_train_id
                    and first_section.si_sstation = start_station.sl_station_id
                    and last_section.si_sell_ticket = 0
                    and first_section.si_section_id <= last_section.si_section_id 
                    and first_section.si_stime > '$go_time'                          
                    and all_section.si_train_id = first_section.si_train_id
                    and all_section.si_section_id >= first_section.si_section_id
                    and all_section.si_section_id <= last_section.si_section_id   
                    and first_st.st_train_id = first_section.si_train_id
                    and first_st.st_section_id = first_section.si_section_id
                    and first_st.st_sdate = '$go_date'                        
                    and last_st.st_train_id = first_section.si_train_id
                    and last_st.st_section_id = last_section.si_section_id        
                    and last_st.st_train_date = first_st.st_train_date            
                    and all_st.st_train_id = first_section.si_train_id
                    and all_st.st_section_id = all_section.si_section_id            
                    and all_st.st_train_date = first_st.st_train_date             
                group by
                    first_section.si_train_id,
                    first_section.si_stime,
                    last_section.si_etime,
                    last_section.si_estation,            
                    last_st.st_edate,
                    first_section.si_sstaytime,
                    first_section.si_sstation,
                    last_section.si_estation
            ) as train1,
            (
                select
                    sl_station_id
                from 
                    stationlist
                where
                    sl_city_name = '$end_city'
            ) as end_station,  
            sectioninfo as first_section,
            sectioninfo as last_section,
            sectioninfo as all_section,
            sectionticket as first_st,
            sectionticket as all_st
        where
            first_section.si_train_id = last_section.si_train_id
            and last_section.si_estation = end_station.sl_station_id
            and last_section.si_sell_ticket = 0
            and first_section.si_section_id <= last_section.si_section_id 
            and all_section.si_train_id = first_section.si_train_id
            and all_section.si_section_id >= first_section.si_section_id
            and all_section.si_section_id <= last_section.si_section_id   
            and first_st.st_train_id = first_section.si_train_id
            and first_st.st_section_id = first_section.si_section_id
            and first_st.st_sdate = (case when (first_section.si_stime - train1.etime) < interval'0min' then train1.edate + interval'1day'
                                     else train1.edate end)          
            and all_st.st_train_id = first_section.si_train_id
            and all_st.st_section_id = all_section.si_section_id             
            and all_st.st_train_date = first_st.st_train_date   
            and
            (
                (
                    train1.station = first_section.si_sstation
                    and train1.train <> first_section.si_train_id
                    and 
                    (case
                    when (first_section.si_stime - train1.etime) < interval'0min' then (first_section.si_stime- train1.etime + interval'24hour')
                    else (first_section.si_stime - train1.etime)
                    end) >= interval'60min'
                    and 
                    (case
                    when (first_section.si_stime - train1.etime) < interval'0min' then (first_section.si_stime- train1.etime + interval'24hour')
                    else (first_section.si_stime - train1.etime)
                    end) <= interval'240min'
                )
                or
                (
                    (
                        select
                            sl_city_name
                        from
                            stationlist
                        where
                            train1.station = sl_station_id
                    )
                    =
                    (
                        select
                            sl_city_name
                        from
                            stationlist
                        where
                            first_section.si_sstation = sl_station_id
                    )
                    and train1.train <> first_section.si_train_id
                    and train1.station <> first_section.si_sstation
                    and
                    (case
                    when (first_section.si_stime- train1.etime) < interval'0min' then (first_section.si_stime- train1.etime + interval'24hour')
                    else (first_section.si_stime- train1.etime)
                    end) >= interval'120min'
                    and 
                    (case
                    when (stime- train1.etime) < interval'0min' then (stime- train1.etime + interval'24hour')
                    else (stime- train1.etime)
                    end) <= interval'240min'
                )
            )
        group by
            train1.train,
            train1.stime,
            train1.etime,
            train1.start_station,
            train1.station,          
            train1.edate,
            train1.YZ,
            train1.RZ,
            train1.YWS,
            train1.YWZ,
            train1.YWX,
            train1.RWS,
            train1.RWX,
            train1.price_YZ,
            train1.price_RZ,
            train1.price_YWS,
            train1.price_YWZ,
            train1.price_YWX,
            train1.price_RWS,
            train1.price_RWX,
            train1.price,
            train1.duration_time,
            first_section.si_train_id,
            first_st.st_sdate,
            first_section.si_sstaytime,
            first_section.si_stime,
            last_section.si_etime,
            first_section.si_sstation,
            last_section.si_estation 
        having
            min(train1.YZ) + min(train1.RZ) + min(train1.YWS)  + min(train1.YWZ) + min(train1.YWX) + min(train1.RWS) + min(train1.RWX) <> 0
            and min(YZ) + min(RZ) + min(YWS) + min(YWZ) + min(YWX) + min(RWS) + min(RWX) <> 0    
    ) as raw_table,
    stationlist as start1_sl,
    stationlist as end1_sl,
    stationlist as start2_sl,
    stationlist as end2_sl
where
    start1_sl.sl_station_id = train1_start_station
    and end1_sl.sl_station_id = train1_station
    and start2_sl.sl_station_id = station
    and end2_sl.sl_station_id = end_station
order by
    price,
    duration_time,
    start_time
limit
    10;
