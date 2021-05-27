<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>userpage</title>
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

<?php
$outdate = $indate = "";
if (!empty($_POST["outdate"]))
    $outdate = test_input($_POST["outdate"]);
else
    $outdate = "2021-05-20";

if (!empty($_POST["indate"]))
    $indate = test_input($_POST["indate"]);
else
    $indate = "2021-06-01";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>


<br/> <br/> <br/> <br/>
<div class="form-container boxf">
    <br><br>
    <h1>用户界面</h1>
    <br><br>
</div>

<br> <br>
<div class="form-container boxf">
    <br><br>
    <h2>历史订单查询</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <br><input type="date" name="outdate" placeholder="出发日期" value="<?php echo $outdate; ?>">
        <input type="date" name="indate" placeholder="到达日期" value="<?php echo $indate; ?>">
        <br><br>
        <button>提交</button>
        <br><br>
    </form>
</div>
<br> <br>

<div class="form-container boxf">
    <br> <br>
    <h2>查询结果</h2>
    <?php
    $dbname = "dbname=lab2";
    $user = "user=postgres";    //In docker,change to dbms
    $password = "password=dbms";
    $dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

    $user_id = $_SESSION['user_id'];

    if (!empty($outdate) & !empty($indate)) {
        $query = "select o_order_id,o_date,o_train_id,A.sl_station_name as sstation,B.sl_station_name as estation,o_price,o_seat_type,o_status
          from orders, stationlist as A, stationlist as B
          where o_user_id = '$user_id'
          and o_date >= '$outdate' 
          and o_date <= '$indate'
          and a.sl_station_id = o_sstation
          and b.sl_station_id = o_estation;";
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());


        echo "<table>\n\t<tr>\n\t\t<th>订单编号</th>\n\t\t<th>日期</th>\n\t\t<th>车次</th>\n\t\t<th>出发站</th>\n\t\t<th>到达站</th>\n";
        echo "\t\t<th>价格</th>\n\t\t<th>座椅种类</th>\n\t\t<th>订单状态</th>\n\t\t<th>取消订单</th>\n\t\t<th>详细信息</th>\n\t<tr>\n";
        while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {

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
                echo "\t\t<td><a href=\"ordercancel.php?order_id=".$line["o_order_id"] . "\">取消订单</a></td>\n";
            }else{
                echo "\t\t<td>已取消</td>\n";
                echo "\t\t<td>---</td>\n";
            }
            echo "\t\t<td><a href=\"orderdetails.php?sstation=".$line["sstation"]. "&estation=".$line["estation"]."&train_id=".$line["o_train_id"]."\">详细信息</a></td>\n";
            echo "\t</tr>\n";
        }

        echo "</table>\n";


        $num = pg_num_rows($result);
        echo "<p>共".$num."条数据</p>";

        pg_free_result($result);
        pg_close($dbconn);
    }

    ?>
    <br> <br>
</div>
<br> <br> <br> <br>
</body>
</html>