<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>order</title>
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
$orderinfo = array("trainid","date","sstation","estation");
for($i = 0;$i < 4;$i++){
    $_SESSION[$orderinfo[$i]] = $_REQUEST[$orderinfo[$i]];
}
$tmp = explode('/',$_REQUEST["seattype"],2);
$_SESSION["seattype"] = $tmp[0];
$price = $tmp[1];
$time = $_REQUEST['time'];
$etime = $_REQUEST['etime'];
$trainnum = $_REQUEST['trainnum'];
$_SESSION['trainnum'] = $trainnum;
$_SESSION['order'] = $price + 5;
$_SESSION['orderid'] = mt_rand();


for($i = 0;$i < 4;$i++){
    $_SESSION[$orderinfo[$i].'1'] = ($trainnum == 2)? $_REQUEST[$orderinfo[$i].'1']:"";
}
$tmp = ($trainnum == 2)?explode('/',$_REQUEST["seattype1"],2):"";
$_SESSION["seattype1"] = ($trainnum == 2)?$tmp[0]:"";
$price1 = ($trainnum == 2)?$tmp[1]:"";
$time1 = ($trainnum == 2)?$_REQUEST['time1']:"";
$etime1 = ($trainnum == 2)?$_REQUEST['etime1']:"";
$_SESSION['orderid1'] = ($trainnum == 2)? mt_rand():"";
$_SESSION['order1'] = ($trainnum == 2)? $price1 + 5:0;
?>

<br/> <br/><br/> <br/>

<div class="form-container boxf">
    <br><br>
    <h1>确认订单</h1>
    <br><br>
</div>

<br/> <br/>
<div class="form-container boxf">
    <br><br>
    <h2>车次</h2>
    <table>
        <tr>
            <th>订单号</th><th>车次</th><th>出发日期</th><th>出发时间</th><th>出发站</th>
            <th>到达时间</th><th>到达站</th><th>座位类型</th><th>票价</th>
            <!-- 出发日期 时间 车站 到达日期 时间 车站 座位类型 车票-->

        </tr>
        <tr>
            <?php

            $arr_name = array("硬座", "软座", "硬卧（上）", "硬卧（中）", "硬卧（下）", "软卧（上）", "软卧（下）");
            echo "\t\t<td>".$_SESSION['orderid']."</td>\n";
            echo "\t\t<td>".$_SESSION['trainid']."</td>\n";
            echo "\t\t<td>".$_SESSION['date']."</td>\n";
            echo "\t\t<td>".$time."</td>\n";
            echo "\t\t<td>".$_SESSION['sstation']."</td>\n";
            echo "\t\t<td>".$etime;
            if($etime < $time) echo "(+1)";
            echo "</td>\n";
            echo "\t\t<td>".$_SESSION['estation']."</td>\n";

            echo "\t\t<td>".$arr_name[$_SESSION['seattype']]."</td>\n";
            echo "\t\t<td>".$price."+5</td>\n";

            if($trainnum == 2){
                echo "\t</tr>\n\t<tr>\n";
                echo "\t\t<td>".$_SESSION['orderid1']."</td>\n";
                echo "\t\t<td>".$_SESSION['trainid1']."</td>\n";
                echo "\t\t<td>".$_SESSION['date1']."</td>\n";
                echo "\t\t<td>".$time1."</td>\n";
                echo "\t\t<td>".$_SESSION['sstation1']."</td>\n";
                echo "\t\t<td>".$etime1;
                if($etime1 < $time1) echo "(+1)";
                echo "</td>\n";
                echo "\t\t<td>".$_SESSION['estation1']."</td>\n";
                echo "\t\t<td>".$arr_name[$_SESSION['seattype1']]."</td>\n";
                echo "\t\t<td>".$price1."+5</td>\n";
            }
            ?>
        </tr>
    </table>
    <p>* 每张车票手续费5元</p>
    <br><br>
</div>

<br/> <br/>

<div class="form-container boxf">
    <br><br>
    <h2>确认订单</h2>
    <h3>总价：
        <?php
        echo $_SESSION['order']+$_SESSION['order1'];
        ?>
    </h3>
    
    <button onclick="location='buy.php'" style="float: left;margin-left: 400px;">购买</button>
    <button onclick="location='welcome.php'" style="float: right;margin-right: 400px;">取消</button>
    <br><br><br><br><br>
</div>

<br/> <br/><br/> <br/>
</body>
</html>


