<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>query train</title>
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
$trainid = $date = "";
$d=strtotime("tomorrow");
if (!empty($_POST["trainid"])) $trainid = test_input($_POST["trainid"]);
if (empty($_POST["date"])) {
    $date = date("Y-m-d",$d);
} else {
    $date = test_input($_POST["date"]);
}

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
    <h1>车次查询</h1>
    <?php
    if ($_SESSION == array()) echo "<p style='color: red'>* 请登录后进行购票</p>"
    ?>
    <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="text" name="trainid" placeholder="车次" value="<?php echo $trainid; ?>">
        <input type="date" name="date" placeholder="日期" value="<?php echo $date; ?>">
        <br>
        <button>提交</button>
    </form>
    <br><br>
</div>

<br> <br>

<div class="form-container boxf">
    <br><br>
    <h1>查询结果</h1>
    <p>座位显示为（价格/余票），点击余票可以进行订票</p>
    <br>
    <?php
    // Connecting, selecting database
    $dbname = "dbname=lab2";
    $user = "user=postgres";    //In docker,change to dbms
    $password = "password=dbms";
    $dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

    if (!empty($trainid)) {
        // Performing SQL query
        $query = "select 
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
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YZ) else 0.0 end) as price_YZ,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_RZ) else 0.0 end) as price_RZ,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YWS) else 0.0 end) as price_YWS,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YWZ) else 0.0 end) as price_YWZ,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_YWX) else 0.0 end) as price_YWX,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_RWS) else 0.0 end) as price_RWS,
            (case when cur.si_sell_ticket = 0 then sum(total.si_price_RWX) else 0.0 end) as price_RWX,
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
";
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

        $arr = array("yz", "rz", "yws", "ywz", "ywx", "rws", "rwx");
        $arr_price = array("price_yz", "price_rz", "price_yws", "price_ywz", "price_ywx", "price_rws", "price_rwx");
        $arr_name = array("硬座", "软座", "硬卧(上)", "硬卧(中)", "硬卧(下)", "软卧(上)", "软卧(下)");

        echo "<table>\n\t<tr>\n";
        echo "\t\t<th>序号</th>\n\t\t<th>车次</th>\n\t\t<th>出发站</th>\n\t\t<th>到达站</th>\n";
        for($i = 0;$i < 7;$i++){
            echo "\t\t<th>$arr_name[$i]</th>\n";
        }
        echo "\t<tr>\n";

        $num = 1;
        while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            if ($num == 1){
                $sstation = $line["sl_station_name"];   //出发站
                $time = $line["ps_out_time"];           //出发时间
            }
            $estation = $line["sl_station_name"];

            echo "\t<tr>\n";
            echo "\t\t<td>$num</td>\n";
            echo "\t\t<td>" . $line["sl_station_name"] . "</td>\n"; //到达站

            $etime =  $line["ps_in_time"];

            echo "\t\t<td>" . $line["ps_in_time"] . "</td>\n";      //到达时间
            echo "\t\t<td>" . $line["ps_out_time"] . "</td>\n";
            for ($i = 0; $i < 7; $i++) {
                $y = $line[$arr[$i]];
                $z = $line[$arr_price[$i]];
                $m = "$i/$z";
                if($z == 0.0 && $y == 0 && $num != 1){
                    echo "\t\t<td>-/-</td>\n";
                }
                else if ($y == 0 && $num != 1) {
                    echo "\t\t<td>$z/$y</td>\n";
                }else if($_SESSION == array()){
                    echo "\t\t<td>$z/$y</td>\n";
                }
                else if ($num != 1) {
                    echo "\t\t<td><a href=\"order.php?trainid=$trainid&date=$date&time=$time&etime=$etime&sstation=$sstation&estation=$estation&seattype=$m&trainnum=1\">$z/$y</a></td>";
                } else {
                    echo "\t\t<td>$z</td>\n";
                }
            }
            $num++;
            echo "\t</tr>\n";
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