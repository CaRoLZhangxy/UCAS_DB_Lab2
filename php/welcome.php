<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>welcome</title>
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

<div class="boxc"><br/> <br/><br/><br/>
    <h1 class="hh"><kbd>欢迎进入订票系统</h1>
    <?php
    if ($_SESSION != array() && session_status() == PHP_SESSION_ACTIVE)
        echo "<p class='pp'><kbd>Welcome " . $_SESSION['account_id'] . "!<br /></p>";
    else
        echo "<p class='pp'><kbd>Welcome guest!<br /></p>";
    ?>

    <span id="cg">2021/05/27 上午12:00:00</span>
    <script>
        setInterval("cg.innerHTML=new Date().toLocaleString()", 1000);
    </script>

    <p>2021 limited</p>


    <br> <br>
    <button onclick="location='register.php'">注册/登录</button>
    <br> <br>
</div>

<br><br>
<div class="boxd">
    <button class="bb" onclick="location='queryTrain.php'">车次查询</button>
</div>

<div class="boxe">
    <button class="bb" onclick="location='queryTicket.php'">起始地查询</button>
</div>

<br><br><br><br>
</body>
</html>