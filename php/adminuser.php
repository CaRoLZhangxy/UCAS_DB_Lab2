<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>user_page</title>
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
    $user_id = $_REQUEST["user_id"];
    $account_id = $_REQUEST["account_id"];
    echo "<h1>查看".$account_id."用户订单</h1>";

    $dbname = "dbname=lab2";
    $user = "user=postgres";    //In docker,change to dbms
    $password = "password=dbms";
    $dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());
    $query = "select o_order_id,o_date,o_train_id,a.sl_station_name as sstation,b.sl_station_name as estation,o_price,o_seat_type,o_status
          from orders, stationlist a, stationlist b
          where o_user_id = '$user_id'
          and a.sl_station_id = o_sstation
          and b.sl_station_id = o_estation;";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    echo "<table>\n\t<tr>\n";
    echo "\t\t<th>订单编号</th>\n\t\t<th>日期</th>\n\t\t<th>车次</th>\n\t\t<th>出发站</th>\n";
    echo "\t\t<th>到达站</th>\n\t\t<th>价格</th>\n\t\t<th>座椅种类</th>\n\t\t<th>订单状态</th>\n";
    echo "\t<tr>\n";

    while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
        echo "\t<tr>\n";
        $num = 0;
        foreach ($line as $col_value) {
            echo "\t\t<td>$col_value</td>\n";
            $num++;
            if($num == 6) break;
        }
        switch ($line["o_seat_type"]) {
            case 0:echo "\t\t<td>硬座</td>\n";break;
            case 1:echo "\t\t<td>软座</td>\n";break;
            case 2:echo "\t\t<td>硬卧（上）</td>\n";break;
            case 3:echo "\t\t<td>硬卧（中）</td>\n";break;
            case 4:echo "\t\t<td>硬卧（下）</td>\n";break;
            case 5:echo "\t\t<td>软卧（上）</td>\n";break;
            case 6:echo "\t\t<td>软卧（下）</td>\n";break;
            default:echo "\t\t<td>---</td>\n";break;
        }
        if($line["o_status"] == 0 || $line["o_status"] == 1){
            echo "\t\t<td>已完成</td>\n";
        }else{
            echo "\t\t<td style='color: #666666'><del>已取消</del></td>\n";
        }
        echo "\t</tr>\n";
    }
    echo "</table>\n";
    pg_free_result($result);
    ?>
    <br><br>
    <button onclick="location='adminpage.php'">返回</button>
    <br><br>
</div>
<br> <br> <br> <br>

</body>
</html>


