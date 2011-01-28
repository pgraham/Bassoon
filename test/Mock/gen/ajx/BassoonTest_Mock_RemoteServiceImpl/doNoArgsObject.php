<?php
require_once '/srv/www/timetracker.zeptech.ca/lib/bassoon/test/Mock/RemoteServiceImpl.php';
$s=new BassoonTest_Mock_RemoteServiceImpl();
try{
 $v=$s->doNoArgsObject();
 echo json_encode($v);
}catch(Exception $e){
 header('HTTP/1.1 500 Internal Server Error');
}