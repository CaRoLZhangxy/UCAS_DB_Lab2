<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>query ticket</title>
    <link rel="stylesheet" href="css/welcome.css">
</head>

<body>
<ul>
    <li><a class="active" href="welcome.php">主页</a></li>
    <?php
    session_start();
    if ($_SESSION != array() && session_status() == PHP_SESSION_ACTIVE) {
        echo "<li style='float:right'><a href='logout.php'>退出登录</a></li>";
        if ($_SESSION['admin'] == 1) {
            echo "<li style='float:right'><a href='adminpage.php'>管理员空间</a></li>";
            echo "<li style='float:right'><a href='adminpage.php'>" . $_SESSION['account_id'] . "</a></li>";
        } else {
            echo "<li style='float:right'><a href='userpage.php'>个人空间</a></li>";
            echo "<li style='float:right'><a href='userpage.php'>" . $_SESSION['account_id'] . "</a></li>";
        }
    } else {
        echo "<li style='float:right'><a href='register.php'>注册</a></li>";
        echo "<li style='float:right'><a href='login.php'>登录</a></li>";
    }
    ?>
</ul>
<br> <br> <br> <br>

<?php
$dbname = "dbname=lab2";
$user = "user=postgres";    //In docker,change to dbms
$password = "password=dbms";
$dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

$d=strtotime("tomorrow");
$scity = $ecity = $date = $time = $transen = "";
if (!empty($_REQUEST["transen"])) $transen = test_input($_REQUEST["transen"]);
if (!empty($_REQUEST["scity"])) $scity = test_input($_REQUEST["scity"]);
if (!empty($_REQUEST["ecity"])) $ecity = test_input($_REQUEST["ecity"]);
if (empty($_REQUEST["date"]))
    $date = date("Y-m-d",$d);
else
    $date = test_input($_REQUEST["date"]);
if (empty($_REQUEST["time"]))
    $time = "00:00";
else
    $time = test_input($_REQUEST["time"]);

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<div class="form-container boxf">
    <br><br>
    <h1>起始地查询</h1>
    <?php
    if ($_SESSION == array()) echo "<p style='color: red'>* 请登录后进行购票</p>"
    ?>
    <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="text" name="scity" placeholder="出发城市" value="<?php echo $scity; ?>">
        <input type="text" name="ecity" placeholder="到达城市" value="<?php echo $ecity; ?>">
        <input type="date" name="date" placeholder="日期" value="<?php echo $date; ?>">
        <input type="time" name="time" placeholder="时间" value="<?php echo $time; ?>">
        <br>
        <button>提交</button>
    </form>
    <br>
    <?php
    $temp = explode("-",$date);
    $ee = mktime(0,0,0,$temp[1],$temp[2],$temp[0]);
    $dd = strtotime("+1 day",$ee);
    $newdate = date("Y-m-d",$dd);
    echo "<a href='queryTicket.php?scity=$ecity&ecity=$scity&date=$newdate' target=\"_blank\">查询返程</a>"
    ?>
    <br><br>
</div>

<br> <br>

<div class="form-container boxf">
    <br><br>
    <h1>查询结果</h1>
    <p>座位显示为（价格/余票），点击余票可以进行订票</p>
    <h2>直达车次</h2>
    <br>
    <?php
    if (!empty($scity) && !empty($ecity)) {
        $query  = "select
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
    else 0.0
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
            sl_city_name = '$scity'
    ) as start_station, 
    (
        select
            sl_station_id
        from 
            stationlist
        where
            sl_city_name = '$ecity'
    ) as end_station,
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
    and first_section.si_section_id <= last_section.si_section_id   
    and first_section.si_stime > '$time'                           
    and all_section.si_train_id = first_section.si_train_id
    and all_section.si_section_id >= first_section.si_section_id
    and all_section.si_section_id <= last_section.si_section_id     
    and first_st.st_train_id = first_section.si_train_id
    and first_st.st_section_id = first_section.si_section_id
    and first_st.st_sdate = '$date'                               
    and all_st.st_train_id = first_section.si_train_id
    and all_st.st_section_id = all_section.si_section_id        
    and all_st.st_train_date = first_st.st_train_date               
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
    10;";
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

        $arr = array("yz", "rz", "yws", "ywz", "ywx", "rws", "rwx");
        $arr_price = array("price_yz", "price_rz", "price_yws", "price_ywz", "price_ywx", "price_rws", "price_rwx");
        $arr_name = array("硬座", "软座", "硬卧（上）", "硬卧（中）", "硬卧（下）", "软卧（上）", "软卧（下）");

        echo "<table>\n\t<tr>\n";
        echo "\t\t<th>序号</th>\n\t\t<th>车次</th>\n\t\t<th>出发时间</th>\n\t\t<th>出发站</th>\n\t\t<th>到达时间</th>\n\t\t<th>到达站</th>\n";
        for($i = 0;$i < 7;$i++){
            echo "\t\t<th>$arr_name[$i]</th>\n";
        }
        echo "\t</tr>\n";

        $num = 1;
        while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            echo "\t<tr>\n";
            echo "\t\t<td>$num</td>\n";
            echo "\t\t<td>" . $line["train"] . "</td>\n";
            echo "\t\t<td>" . $line["start_time"] . "</td>\n";
            echo "\t\t<td>" . $line["sstation"] . "</td>\n";
            $etime = $line["etime"];
            echo "\t\t<td>".$line["etime"];
            if($line["etime"] < $line["start_time"]) echo "(+1)";
            echo "</td>\n";
            echo "\t\t<td>" . $line["estation"] . "</td>\n";



            for ($i = 0; $i < 7; $i++) {
                $y = $line[$arr[$i]];
                $z = $line[$arr_price[$i]];
                $m = "$i/$z";
                if($z == 0.0 && $y == 0){
                    echo "\t\t<td>-/-</td>\n";
                }
                else if ($y == 0 || ($y && $_SESSION == array())) {
                    echo "\t\t<td>$z/$y</td>\n";
                }else if ($y) {
                    echo "\t\t<td><a href=\"order.php?trainid=".$line["train"]."&date=$date&etime=$etime&time=".$line["start_time"]."&sstation=".$line["sstation"]."&estation=".$line["estation"]."&seattype=$m&trainnum=1\">$z/$y</a></td>";
                }
            }
            $num++;
            echo "\t</tr>\n";
        }
        echo "</table>\n";
        pg_free_result($result);
    }
    ?>
    <br><br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="scity" value="<?php echo $scity; ?>">
        <input type="hidden" name="ecity" value="<?php echo $ecity; ?>">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <input type="hidden" name="time" value="<?php echo $time; ?>">
        <input type="hidden" name="transen" value="nihao">
        <button>查询换乘</button>
    </form>
    <br><br>
</div>
    <br><br>
<div class="boxf" style="overflow-x:auto;">
    <br><br>
    <h2>换乘车次</h2>
    <br>

    <?php
    if (!empty($scity) && !empty($ecity) && !empty($transen)) {
        $query  = "select
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
                            sl_city_name = '$scity'
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
                    and first_section.si_stime > '$time'                          
                    and all_section.si_train_id = first_section.si_train_id
                    and all_section.si_section_id >= first_section.si_section_id
                    and all_section.si_section_id <= last_section.si_section_id   
                    and first_st.st_train_id = first_section.si_train_id
                    and first_st.st_section_id = first_section.si_section_id
                    and first_st.st_sdate = '$date'                         
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
                    sl_city_name = '$ecity'
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
    10;";

        //echo $query;
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());
        $arr = array("yz", "rz", "yws", "ywz", "ywx", "rws", "rwx");
        $arr_price = array("price_yz", "price_rz", "price_yws", "price_ywz", "price_ywx", "price_rws", "price_rwx");
        $arr_name = array("硬座", "软座", "硬卧(上)", "硬卧(中)", "硬卧(下)", "软卧(上)", "软卧(下)");

        echo "<table style='width: 2000px;margin-right: 130px;margin-left: 130px'>\n\t<tr>\n";
        echo "\t\t<th>序号</th>\n\t\t<th>车次</th>\n\t\t<th>出发日期</th>\n\t\t<th>出发时间</th>\n";
        echo "\t\t<th>出发站</th>\n\t\t<th>到达时间</th>\n\t\t<th>到达站</th>\n\t\t<th>中转时间</th>\n\t\t<th>总时间</th>\n";
        for($i = 0;$i < 7;$i++){
            echo "\t\t<th>$arr_name[$i]</th>\n";
        }
        echo "\t\t<th>购买</th>\n";
        echo "\t</tr>\n";

        $num = 1;
        while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            $stime = $line["start_time"];
            $durtime = $line["duration_time"];

            $wtime = $line["wait_time"];
            $etime = $line["train1_etime"];

            $id = $line["train1_id"];
            $ssta = $line["sstation"];
            $esta = $line["trans_station1"];

            $id1 = $line["train2_id"];
            $ssta1 = $line["trans_station2"];
            $esta1 = $line["estation"];
            $date1 = $line["train2_sdate"];
            $stime1 = $line["train2_stime"];
            $etime1 = $line["train2_etime"];



            echo "\t<tr>\n";
            echo "\t\t<td rowspan='2'>$num</td>\n";

            echo "\t\t<td>$id</td>\n";
            echo "\t\t<td>$date</td>\n";
            echo "\t\t<td>$stime</td>\n";
            echo "\t\t<td>$ssta</td>\n";
            echo "\t\t<td>$etime";
            if($etime < $stime) echo "(+1)";
            echo "</td>\n";

            $hour = (int)($durtime / 60);
            $min = $durtime % 60;
            $du_time = $hour."时".$min."分";
            echo "\t\t<td>$esta</td>\n";
            $tmp = explode(':',$wtime,3);
            $hour1 = $tmp[0];
            $min1 = $tmp[1];
            $wtime1 = $hour1."时".$min1."分";
            echo "\t\t<td rowspan='2'>$wtime1</td>\n";
            echo "\t\t<td rowspan='2'>$du_time</td>\n";

            if($_SESSION == array()){
                for ($i = 0; $i < 7; $i++) {
                    $y = $line["train1_" . $arr[$i]];
                    $z = $line["train1_" . $arr_price[$i]];
                    $m = "$i/$z";
                    if ($z == 0.0 && $y == 0) {
                        echo "\t\t<td>-/-</td>\n";
                    } else {
                        echo "\t\t<td>$z/$y</td>\n";
                    }
                }
                echo "\t\t<td rowspan='2'>-</td>\n";
                echo "\t</tr>\n";

                echo "\t<tr>\n";
                echo "\t\t<td>$id1</td>\n";
                echo "\t\t<td>$date1</td>\n";
                echo "\t\t<td>$stime1</td>\n";
                echo "\t\t<td>$ssta1</td>\n";
                echo "\t\t<td>$etime1";
                if($etime1 < $stime1) echo "(+1)";
                echo "</td>\n";
                echo "\t\t<td>$esta1</td>\n";
                for ($i = 0; $i < 7; $i++) {
                    $y = $line[$arr[$i]];
                    $z = $line[$arr_price[$i]];
                    $m = "$i/$z";
                    if ($z == 0.0 && $y == 0) {
                        echo "\t\t<td>-/-</td>\n";
                    } else {
                        echo "\t\t<td>$z/$y</td>\n";
                    }
                }
            }
            else{

            echo "<form class='ff' action='order.php' method='post'>";
            echo "<input type=\"hidden\" name=\"trainnum\" value=2>";
            echo "<input type=\"hidden\" name=\"trainid\" value=$id>";
            echo "<input type=\"hidden\" name=\"time\" value=$stime>";
            echo "<input type=\"hidden\" name=\"etime\" value=$etime>";
            echo "<input type=\"hidden\" name=\"date\" value=$date>";
            echo "<input type=\"hidden\" name=\"sstation\" value=$ssta>";
            echo "<input type=\"hidden\" name=\"estation\" value=$esta>";

            echo "<input type=\"hidden\" name=\"trainid1\" value=$id1>";
            echo "<input type=\"hidden\" name=\"date1\" value=$date1>";
            echo "<input type=\"hidden\" name=\"time1\" value=$stime1>";
            echo "<input type=\"hidden\" name=\"etime1\" value=$etime1>";
            echo "<input type=\"hidden\" name=\"sstation1\" value=$ssta1>";
            echo "<input type=\"hidden\" name=\"estation1\" value=$esta1>";

            for ($i = 0; $i < 7; $i++) {
                $y = $line["train1_" . $arr[$i]];
                $z = $line["train1_" . $arr_price[$i]];
                $m = "$i/$z";
                if ($z == 0.0 && $y == 0) {
                    echo "\t\t<td>-/-</td>\n";
                } else {
                    echo "\t\t<td><label><input name=\"seattype\" type=\"radio\" value=$m>$z/$y</label></td>\n";
                }
            }
            echo "\t\t<td rowspan='2'>";
            echo "<input type='submit' value='提交'>";
            echo "</td>\n";
            echo "\t</tr>\n";

            echo "\t<tr>\n";
            echo "\t\t<td>$id1</td>\n";
            echo "\t\t<td>$date1</td>\n";
            echo "\t\t<td>$stime1</td>\n";
            echo "\t\t<td>$ssta1</td>\n";

                echo "\t\t<td>$etime1";
                if($etime1 < $stime1) echo "(+1)";
                echo "</td>\n";
            echo "\t\t<td>$esta1</td>\n";

            for ($i = 0; $i < 7; $i++) {
                $y = $line[$arr[$i]];
                $z = $line[$arr_price[$i]];
                $m = "$i/$z";
                if ($z == 0.0 && $y == 0) {
                    echo "\t\t<td>-/-</td>\n";
                } else {
                    echo "\t\t<td><label><input name=\"seattype1\" type=\"radio\" value=$m>$z/$y</label></td>\n";
                }
            }
            echo "</form>";
            }
            echo "\t</tr>\n";
            $num++;
        }
        echo "</table>\n";
        pg_free_result($result);
        pg_close($dbconn);
    }
    ?>
    <br><br>
</div>
<br><br><br><br>
</body>
</html>