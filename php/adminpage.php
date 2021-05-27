<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>adminpage</title>
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
    <h1>管理员页面</h1>
    <br><br>
</div>

<?php
$dbname = "dbname=lab2";
$user = "user=postgres";    //In docker,change to dbms
$password = "password=dbms";
$dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());
?>

<br> <br><br> <br>
<div class="form-container boxf">
    <?php
    echo "<br><br><h2>总订单数</h2>";
    $query = "select count(o_order_id) from orders;";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $line = pg_fetch_array($result, null, PGSQL_BOTH);
    echo $line[0] . "\n";
    pg_free_result($result);

    echo "<br><br><h2>总票价</h2>";
    $query = "select sum(o_price) from orders;";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $line = pg_fetch_array($result, null, PGSQL_BOTH);
    echo $line[0] . "\n";
    pg_free_result($result);

    echo "<br><br><h2>热点车次排序</h2>";
    $query = "select distinct o_train_id,count(o_train_id) 
            from orders 
            group by o_train_id 
            order by count(o_train_id) desc 
            limit 10;";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $num = 1;
    echo "<table>\n\t<tr>\n\t\t<th>序号</th>\n\t\t<th>车次</th>\n\t\t<th>次数</th>\n\t<tr>\n";
    while ($line = pg_fetch_array($result, null, PGSQL_BOTH)) {
        echo "\t<tr>\n\t\t<td>$num</td>\n\t\t<td>".$line[0]."</td>\n\t\t<td>".$line[1]."</td>\n\t<tr>\n";
        $num++;
    }
    echo "</table>\n<br><br>";
    pg_free_result($result);
    ?>
</div>
<br> <br><br> <br>


<div class="form-container boxf">
    <br><br>
    <h2>当前注册用户列表</h2>
    <?php
    $query = "select u_account_id,u_user_name,u_pnumber,u_user_id,u_credit_card,u_password from users where u_admin<>1;";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $num = pg_num_rows($result);

    echo "<table>\n\t<tr>\n\t\t<th>用户名</th>\n\t\t<th>姓名</th>\n\t\t<th>手机号</th>\n\t\t<th>身份证号</th>\n";
    echo "\t\t<th>信用卡号</th>\n\t\t<th>密码</th>\n\t\t<th>点击查看订票信息</th>\n\t<tr>\n";
    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        echo "\t<tr>\n";
        foreach ($line as $col_value) {
            echo "\t\t<td>$col_value</td>\n";
        }
        echo "\t\t<td>";
        echo "<a href=\"adminuser.php?user_id=" . $line["u_user_id"] . "&account_id=" . $line["u_account_id"] . "\">查看订单</a>";
        echo "</td>\n";
        echo "\t</tr>\n";
    }
    echo "</table>\n<br><br>";
    pg_free_result($result);
    pg_close($dbconn);
    ?>
</div>

<br> <br> <br> <br>

</body>
</html>

