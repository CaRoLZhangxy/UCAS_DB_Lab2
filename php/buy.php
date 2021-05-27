<?php
session_start();
$dbname = "dbname=lab2";
$user = "user=postgres";    //In docker,change to dbms
$password = "password=dbms";
$dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

$orderid = $_SESSION['orderid'];
$trainid = $_SESSION['trainid'];
$date = $_SESSION['date'];
$sstation = $_SESSION['sstation'];
$estation = $_SESSION['estation'];
$seattype = $_SESSION['seattype'];
$price = $_SESSION['order'];
$userid = $_SESSION['user_id'];


$query = "select sl1.sl_station_id,sl2.sl_station_id
from stationlist as sl1,stationlist as sl2
where sl1.sl_station_name = '$sstation'
and   sl2.sl_station_name = '$estation';";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
$line = pg_fetch_array($result, null, PGSQL_BOTH);
$sstation = $line[0];
$estation = $line[1];


$query = "insert into orders
  values ('$orderid','$userid','$date','$trainid','$sstation','$estation','$price','$seattype',1,0);";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());


$query = "update sectionticket set ";
switch ($seattype) {
    case 0:
        $query .= "st_tnum_YZ = st_tnum_YZ - 1 ";
        break;
    case 1:
        $query .= "st_tnum_RZ = st_tnum_RZ - 1 ";
        break;
    case 2:
        $query .= "st_tnum_YWS = st_tnum_YWS - 1 ";
        break;
    case 3:
        $query .= "st_tnum_YWZ = st_tnum_YWZ - 1 ";
        break;
    case 4:
        $query .= "st_tnum_YWX = st_tnum_YWX - 1 ";
        break;
    case 5:
        $query .= "st_tnum_RWS = st_tnum_RWS - 1 ";
        break;
    case 6:
        $query .= "st_tnum_RWX = st_tnum_RWX - 1 ";
        break;
    default:
        echo "<script>alert('error!'); window.location.href='welcome.php';</script>";
        break;
}
$query .= "from sectioninfo as si1,sectioninfo as si2
  where 
      st_train_id = '$trainid' 
      and si1.si_train_id = st_train_id
      and si2.si_train_id = st_train_id
      and si1.si_sstation = '$sstation'
      and si2.si_estation = '$estation'
      and st_sdate = '$date' 
      and (st_section_id >= si1.si_section_id) 
      and (st_section_id <= si2.si_section_id);";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());



if ($_SESSION['trainnum'] == 2) {
    $orderid_old = $orderid; 
    $orderid = $_SESSION['orderid1'];
    $trainid = $_SESSION['trainid1'];
    $date = $_SESSION['date1'];
    $sstation = $_SESSION['sstation1'];
    $estation = $_SESSION['estation1'];
    $seattype = $_SESSION['seattype1'];
    $price = $_SESSION['order1'];

    
    $query = "update orders set o_another_id = $orderid where o_order_id = $orderid_old;";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    $query = "select sl1.sl_station_id,sl2.sl_station_id
from stationlist as sl1,stationlist as sl2
where sl1.sl_station_name = '$sstation'
and   sl2.sl_station_name = '$estation';";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $line = pg_fetch_array($result, null, PGSQL_BOTH);
    $sstation = $line[0];
    $estation = $line[1];


    $query = "insert into orders
  values ('$orderid','$userid','$date','$trainid','$sstation','$estation','$price','$seattype',1,$orderid_old);";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

    $query = "update sectionticket set ";
    switch ($seattype) {
        case 0:
            $query .= "st_tnum_YZ = st_tnum_YZ - 1 ";
            break;
        case 1:
            $query .= "st_tnum_RZ = st_tnum_RZ - 1 ";
            break;
        case 2:
            $query .= "st_tnum_YWS = st_tnum_YWS - 1 ";
            break;
        case 3:
            $query .= "st_tnum_YWZ = st_tnum_YWZ - 1 ";
            break;
        case 4:
            $query .= "st_tnum_YWX = st_tnum_YWX - 1 ";
            break;
        case 5:
            $query .= "st_tnum_RWS = st_tnum_RWS - 1 ";
            break;
        case 6:
            $query .= "st_tnum_RWX = st_tnum_RWX - 1 ";
            break;
        default:
            echo "<script>alert('error!'); window.location.href='welcome.php';</script>";
            break;
    }
    $query .= "from sectioninfo as si1,sectioninfo as si2
  where 
      st_train_id = '$trainid' 
      and si1.si_train_id = st_train_id
      and si2.si_train_id = st_train_id
      and si1.si_sstation = $sstation
      and si2.si_estation = $estation
      and st_sdate = '$date' 
      and (st_section_id >= si1.si_section_id) 
      and (st_section_id <= si2.si_section_id);";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
}
echo "<script>alert('购票成功'); location.href='welcome.php';</script>";

pg_close($dbconn);
?>


