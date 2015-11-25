&lt;?php
 $export = fopen(dirname(__FILE__).'/export.sql','w');
 function export($sql) {
 global $export;
 fwrite($export,$sql);
 ob_flush();
 }
 function trace($msg){
 echo $msg.'&lt;br&gt;';
 ob_flush();
 }
 mysql_connect('localhost','root','');
 mysql_select_db('baza');
 mysql_query('set names utf8');

 $res=mysql_query('show tables');

 while($tbl=mysql_fetch_array($res)){
 $table=$tbl[0];
 $r=mysql_query('show create table `'
 .mysql_real_escape_string($table).'`');
 $struct=mysql_fetch_array($r);
 $sql_struct[$table]=$struct[1].';';
 }

 export(&quot;set names utf8;\n&quot;);

 foreach($sql_struct as $tbl_name=&gt;$crt_str){
 trace('Идет экспорт '.$tbl_name);
 export(&quot;DROP TABLE IF EXISTS `&quot;.$tbl_name.&quot;`;\n&quot;);
 export($crt_str.&quot;\n&quot;);
 export(&quot;LOCK TABLES `&quot;.$tbl_name.&quot;` WRITE;\n&quot;);
 mysql_query('LOCK TABLES `'.$tbl_name.'` READ');
 $res=mysql_query('select * from `'.$tbl_name.'`');
 $insert_str='insert into `'.$tbl_name.'` values ';
 while($item=mysql_fetch_assoc($res)){
 foreach($item as $k=&gt;$v){
 $item[$k]=mysql_real_escape_string($v);
 }
 export($insert_str.'(&quot;'.implode('&quot;,&quot;',$item).'&quot;);'.&quot;\n&quot;);
 }
 export(&quot;UNLOCK TABLES;\n&quot;);
 mysql_query('UNLOCK TABLES');
 }
 export('-- end of export');
 trace('База была успешно экспортирована');
?&gt;