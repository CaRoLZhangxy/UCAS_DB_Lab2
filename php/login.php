<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>login</title>
    <link rel="stylesheet" href="css/welcome.css">
</head>

<body>

<ul>
    <li><a class="active" href="welcome.php">主页</a></li>
    <?php
    session_start();
    if ($_SESSION != array() && session_status() == PHP_SESSION_ACTIVE){
        echo "<li style='float:right'><a href='logout.php'>退出登录</a></li>";
        if($_SESSION['admin'] == 1){
            echo "<li style='float:right'><a href='adminpage.php'>管理员空间</a></li>";
            echo "<li style='float:right'><a href='adminpage.php'>".$_SESSION['account_id']."</a></li>";
        }else {
            echo "<li style='float:right'><a href='userpage.php'>个人空间</a></li>";
            echo "<li style='float:right'><a href='userpage.php'>" . $_SESSION['account_id'] . "</a></li>";
        }
    }
    else{
        echo "<li style='float:right'><a href='register.php'>注册</a></li>";
        echo "<li style='float:right'><a href='login.php'>登录</a></li>";
    }
    ?>
</ul>

<?php
    $accountid = $pwd = "";
    if (!empty($_POST["accountid"])) $accountid = test_input($_POST["accountid"]);
    if (!empty($_POST["password"])) $pwd = test_input($_POST["password"]);
    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

?>

<br id="signin"> <br> <br> <br>
<div class="boxg" id="boxg">
    <div class="form-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <br><br><h1>登录</h1>
            <span>使用您的帐号</span>
            <input type="text" placeholder="用户名" name = "accountid" value="<?php echo $accountid; ?>">
            <input type="password" placeholder="密码" name = "password" value="<?php echo $pwd; ?>">
            <p>还未注册？<a href="register.php">点击这里</a></p>
            <button>登录</button>
        </form>

        <?php
        $dbname = "dbname=lab2";
        $user = "user=postgres";    //In docker,change to dbms
        $password = "password=dbms";
        $dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

        if(!empty($accountid) && !empty($pwd)){
            $query="select u_account_id , u_password ,u_admin, u_user_id
                    from users
                    where u_account_id='$accountid' and u_password='$pwd';";
            $result = pg_query($query) or die('Query failed: ' . pg_last_error());
            $num=pg_num_rows($result);

            if($num)
            {
                $arr = pg_fetch_array($result,0,PGSQL_ASSOC);
                $_SESSION['account_id'] = $arr['u_account_id'];
                $_SESSION['user_id'] = $arr['u_user_id'];
                $_SESSION['admin'] = $arr['u_admin'];

                pg_close($dbconn);
                if($arr['u_admin'] == 0)
                    echo "<script>alert('成功登录'); location.href='welcome.php';</script>";
                else
                    echo "<script>alert('管理员成功登录'); location.href='welcome.php';</script>";
            }
            else {
                pg_close($dbconn);
                echo "<script>alert('密码不正确！');location.href='login.php';</script>";
            }
        }
        ?>
    </div>
</div>


</body>
</html>