<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>orderdetails</title>
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

<br/> <br/><br/> <br/>
<div class="form-container boxf">
    <br><br>
    <?php
    $dbname = "dbname=lab2";
    $user = "user=postgres";    //In docker,change to dbms
    $password = "password=dbms";
    $dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

    $train_id = $_REQUEST["train_id"];
    echo "<h1>订单详细信息</h1><br><br>";
    $query = "select 
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
            ps_train_id = '$train_id'
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
            cur.si_train_id = '$train_id'
    ) as info
on
    station.ps_station_id = info.estation
order by
    (case when info.section_id is null then 0 else info.section_id end);";

    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    echo "<table>\n\t<tr>\n";
    echo "\t\t<th>站名</th>\n\t\t<th>到达时间</th>\n\t\t<th>出发时间</th>\n\t\t<th>停留时间</th>\n";
    echo "\t<tr>\n";
    $flag = 0;
    while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
        echo "\t<tr>\n";
        if($line["sl_station_name"] == $_REQUEST["sstation"] || $line["sl_station_name"] == $_REQUEST["estation"] ){
            foreach ($line as $col_value) {
                echo "\t\t<td style=\"font-weight: bold\">$col_value</td>\n";
            }
            $flag = !$flag;
        }else if ($flag == 1){
            foreach ($line as $col_value) {
                echo "\t\t<td>$col_value</td>\n";
            }
        }else if ($flag == 0){
            foreach ($line as $col_value) {
                echo "\t\t<td style='color: #666666'>$col_value</td>\n";
            }
        }
        echo "\t</tr>\n";
    }
    echo "</table>\n";
    pg_free_result($result);
    ?>
    <br><br>
    <button onclick="location='userpage.php'">返回</button>
    <br><br>
</div>
<br> <br> <br> <br>

</body>
</html>