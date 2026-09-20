<?php
$supa_host = "aws-1-us-east-2.pooler.supabase.com"; 
$supa_user = "postgres.qekmbsymmtqlcnprkhwn";
$supa_password = "jhon0102";
$supa_dbname = "postgres";
$supa_port = "6543";

$local_host = "localhost";
$local_user = "postgres";
$local_password = "sebas0102";
$local_dbname = "market-app"; 
$local_port = "5432";

$supa_data_connection = "
    host=$supa_host
    user=$supa_user
    password=$supa_password
    dbname=$supa_dbname
    port=$supa_port
";

$local_data_connection = "
    host=$local_host
    user=$local_user
    password=$local_password
    dbname=$local_dbname
    port=$local_port
";

$conn_supa = pg_connect($supa_data_connection);
$conn_local = pg_connect($local_data_connection);



?>

