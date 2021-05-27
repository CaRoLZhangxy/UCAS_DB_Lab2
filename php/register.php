<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>register</title>
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
    $accountid = $username = $userid = $pnumber = $creditcard = $pwd = "";
    if (!empty($_POST["accountid"])) $accountid = test_input($_POST["accountid"]);
    if (!empty($_POST["username"])) $username = test_input($_POST["username"]);
    if (!empty($_POST["userid"])) $userid = test_input($_POST["userid"]);
    if (!empty($_POST["pnumber"])) $pnumber = test_input($_POST["pnumber"]);
    if (!empty($_POST["creditcard"])) $creditcard = test_input($_POST["creditcard"]);
    if (!empty($_POST["password"]))  $pwd = test_input($_POST["password"]);

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>

<br id="signup"> <br> <br> <br>
<div class="boxg" id="boxg">
    <div class="form-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <br><br><h1>注册</h1>
            <span>注册您的账户</span>
            <input type="text" name="accountid" placeholder="用户名" value="<?php echo $accountid; ?>">
            <input type="text" placeholder="姓名" name="username" value="<?php echo $username; ?>">
            <input type="text" placeholder="身份证" name="userid" size="18" value="<?php echo $userid; ?>">
            <input type="tel" placeholder="手机号" name="pnumber" value="<?php echo $pnumber; ?>">
            <input type="number" placeholder="信用卡" name="creditcard" size="16" value="<?php echo $creditcard; ?>">
            <input type="password" placeholder="密码" name="password" value="<?php echo $pwd; ?>">
            <p>已有账号？<a href="login.php">点击这里</a></p>
            <button>注册</button>
        </form>


        <?php
        $dbname = "dbname=lab2";
        $user = "user=postgres";    //In docker,change to dbms
        $password = "password=dbms";
        $dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

        if(!empty($accountid) && !empty($username) && !empty($userid) &&
            !empty($pnumber) && !empty($creditcard) && !empty($pwd)) {
            $array = array(
                "u_user_id" => $userid,
                "u_user_name" => $username,
                "u_pnumber" => $pnumber,
                "u_credit_card" => $creditcard,
                "u_account_id" => $accountid,
                "u_admin" => "0",
                "u_password" => $pwd
            );
            $result = pg_insert($dbconn, 'users', $array);
            pg_close($dbconn);
            if ($result) {
                echo "<script>alert('注册成功');location.href='login.php';</script>";
            } else {
                echo "<script>alert('内容重复，注册失败');location.href='register.php';</script>";
            }
        }
        ?>


    </div>
</div>

</body>
</html>