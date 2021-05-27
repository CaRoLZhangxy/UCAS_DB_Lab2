<?php
$dbname = "dbname=lab2";
$user = "user=postgres";    //In docker,change to dbms
$password = "password=dbms";
$dbconn = pg_connect("$dbname $user $password") or die('Could not connect: ' . pg_last_error());

$order_id = $_REQUEST['order_id'];
$cancel = "update orders set o_status = -1,o_price = 5 where o_order_id = $order_id;";
pg_query($cancel);

$query = "select o_train_id,o_date,o_sstation,o_estation,o_seat_type,o_another_id from orders where o_order_id = $order_id;";
$result = pg_query($query) or die('Query2 failed: '.pg_last_error());
$line = pg_fetch_array($result, 0, PGSQL_ASSOC);

$query = "update sectionticket set ";
switch ($line["o_seat_type"]) {
    case 0:$query .="st_tnum_YZ = st_tnum_YZ + 1 ";break;
    case 1:$query .="st_tnum_RZ = st_tnum_RZ + 1 ";break;
    case 2:$query .="st_tnum_YWS = st_tnum_YWS + 1 ";break;
    case 3:$query .="st_tnum_YWZ = st_tnum_YWZ + 1 ";break;
    case 4:$query .="st_tnum_YWX = st_tnum_YWX + 1 ";break;
    case 5:$query .="st_tnum_RWS = st_tnum_RWS + 1 ";break;
    case 6:$query .="st_tnum_RWX = st_tnum_RWX + 1 ";break;
    default:echo "<script>alert('error!'); location.href='userpage.php';</script>";break;
}
$query .= "from sectioninfo as si1,sectioninfo as si2
  where 
      st_train_id = '".$line["o_train_id"]."' 
      and si1.si_train_id = st_train_id
      and si2.si_train_id = st_train_id
      and si1.si_sstation = '".$line["o_sstation"]."'
      and si2.si_estation = '".$line["o_estation"]."'
      and st_sdate = '".$line["o_date"]."'
      and (st_section_id >= si1.si_section_id) 
      and (st_section_id <= si2.si_section_id);";
pg_query($query);

if($line["o_another_id"] != 0)
{
    $order_id = $line["o_another_id"];
    $cancel = "update orders set o_status = -1,o_price = 5 where o_order_id = $order_id;";
    pg_query($cancel);

    $query = "select o_train_id,o_date,o_sstation,o_estation,o_seat_type,o_another_id from orders where o_order_id = $order_id;";
    $result = pg_query($query) or die('Query2 failed: '.pg_last_error());
    $line = pg_fetch_array($result, 0, PGSQL_ASSOC);

    $query = "update sectionticket set ";
    switch ($line["o_seat_type"]) {
        case 0:$query .="st_tnum_YZ = st_tnum_YZ + 1 ";break;
        case 1:$query .="st_tnum_RZ = st_tnum_RZ + 1 ";break;
        case 2:$query .="st_tnum_YWS = st_tnum_YWS + 1 ";break;
        case 3:$query .="st_tnum_YWZ = st_tnum_YWZ + 1 ";break;
        case 4:$query .="st_tnum_YWX = st_tnum_YWX + 1 ";break;
        case 5:$query .="st_tnum_RWS = st_tnum_RWS + 1 ";break;
        case 6:$query .="st_tnum_RWX = st_tnum_RWX + 1 ";break;
        default:echo "<script>alert('error!'); location.href='userpage.php';</script>";break;
    }
    $query .= "from sectioninfo as si1,sectioninfo as si2
    where 
      st_train_id = '".$line["o_train_id"]."' 
      and si1.si_train_id = st_train_id
      and si2.si_train_id = st_train_id
      and si1.si_sstation = '".$line["o_sstation"]."'
      and si2.si_estation = '".$line["o_estation"]."'
      and st_sdate = '".$line["o_date"]."'
      and (st_section_id >= si1.si_section_id) 
      and (st_section_id <= si2.si_section_id);";
    pg_query($query);
}
//echo $query;
pg_free_result($result);
pg_close($dbconn);

echo "<script>alert('取消成功'); location.href='userpage.php';</script>";
?>

