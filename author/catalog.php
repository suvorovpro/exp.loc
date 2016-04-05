<?php
Error_reporting(7);
$link = mysqli_connect("localhost", "root", "", "exploit");
if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}

include ('acheck.php'); // проверка на логин
if(defined("IS_LOGIN")!=true or IS_LOGIN!="yes"){ header("Location: index.php"); exit();}


//printf("Errormessage: %s\n", mysqli_error($link));

//Удаление записей
if(isset($_GET['с_delete'])){
   mysqli_query($link, "DELETE FROM `exploits` WHERE id='".intval($_GET['с_delete'])."'" );	
   
}
//конец Удаление записей

//вывод категорий
if(!isset($_GET['fid_cat']) or $_GET['fid_cat']=='0') {$cat=""; $clink="";}
else { $cat="WHERE `id_category`=".intval($_GET['fid_cat']); $clink="&fid_cat=".intval($_GET['fid_cat']);}

//поиск
if(isset($_GET['findr'])){
    $squer = trim($_GET['findq']); 
    $squer = mysqli_real_escape_string($link,$squer);
    $squer = htmlspecialchars($squer);
switch ($_GET['findr']):
    case 1:
        $fquer="WHERE `title` LIKE '%$squer%'";
        break;
    case 2:
        $fquer="WHERE `full_text` LIKE '%$squer%'";
        break;
    case 3:
        $fquer="WHERE `author` LIKE '%$squer%'";
        break;
    case 4:
        $fquer="WHERE `cve` LIKE '%$squer%'";
        break;
    case 5:
        $fquer="WHERE `tags` LIKE '%$squer%'";
        break;
    default:
        header("Location: catalog.php");
endswitch;  
    
    $result00 = mysqli_query($link,"SELECT COUNT(*) as count FROM `exploits` ".$fquer);
    $flink="&findq=".$squer."&findr=".$_GET['findr'];
} else {
    $result00 = mysqli_query($link,"SELECT COUNT(*) as count FROM `exploits`".$cat);
}
//конец поиска




// постраничная навигация 
$num = 2;
$page = $_GET['page'];
//$result00 = mysqli_query($link,"SELECT COUNT(*) as count FROM `exploits`".$cat);
$temp = mysqli_fetch_row($result00);
$posts = $temp[0];
$total = (($posts - 1) / $num) + 1;
$total =  intval($total);
$page = intval($page);
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total;
$start = $page * $num - $num;	
// конец постраничной навигации




include('tpl/header.php');
?>


      <h2>Каталог эксплоитов</h2><br><br><br>
<div class="row">
<form method="GET" action="catalog.php">
     <input type="text" class="form-control" id="findq" name="findq" id="exampleInputEmail2" placeholder="Поиск" style="width:40%; margin: 0 30px 10px 30px"> Искать по
    <select id="findr" name="findr" style="width:150px;margin-left:10px">   
        <option value="1">Названию</option>
        <option value="2">Тексту</option>
        <option value="3">Автору</option>
        <option value="4">CVE</option>
    </select>
    <button type="submit" class="btn btn-default" style="margin-bottom:10px">Поиск</button>
</form>
</div>

<table class="table table-striped table-bordered table-hover">
    <tr><th>Дата</th><th>
       <form method="get" action="catalog.php">
        <select id="fid_cat" name="fid_cat" onchange="submit();">   
        <option value="0">Все категории</option>  
<?php
$res = mysqli_query($link,"SELECT * FROM `category` ORDER BY `id`");
while($tab_row = mysqli_fetch_assoc($res)) {
    if($_GET['fid_cat']==$tab_row['id']) {$sel="selected";}
    else {$sel="";}
    echo "<option value=\"".$tab_row['id']."\" ".$sel.">".$tab_row['ctitle']."</option>";    
}
?>
        </select>
        </form>
    </th><th>Эксплоит</th><th>CVE</th><th>Автор</th><th>Тэги</th><th>del</th></tr>

<?php
    
if(isset($_GET['findr'])){
    $result = mysqli_query($link,"SELECT id,id_category,title,cve,author,date_add,tags FROM `exploits`  ".$fquer."  ORDER BY `id` DESC  LIMIT $start, $num ");
} else {
    $result = mysqli_query($link,"SELECT id,id_category,title,cve,author,date_add,tags FROM `exploits`  ".$cat."  ORDER BY `id` DESC  LIMIT $start, $num ");
}
while($exp_tab = mysqli_fetch_assoc($result)) {
  
    $cat_tab=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM `category` WHERE `id`=".$exp_tab['id_category'].""));
    //тэги
    $tags=explode(",", $exp_tab['tags']);
    $ech= "<tr><td>".$exp_tab['date_add']."</td><td>".$cat_tab['ctitle']."</td><td><a href='exploit.php?exid=".$exp_tab['id']."'>".$exp_tab['title']."</a></td><td>".$exp_tab['cve']."</td><td>".$exp_tab['author']."</td><td>";    
    foreach($tags as $tval){
        $tval = trim($tval); 
        $ech.="<a href=\"catalog.php?findr=5&findq=$tval\">$tval</a>  ";
    }
    $ech.="</td><td><span class=\"badge badge-important\"><a href=\"catalog.php?с_delete=".$exp_tab['id']."\">del</a></span></td></tr>";
    echo $ech;
}
?>
    
</table>
<?php
  	// опять постраничная навигация
echo <<<HTML
		<div class="row" style="padding: 17px 0 10px 15px">
			<div class="col-md-10">
HTML;
// Проверяем нужны ли стрелки назад
if ($page != 1) $pervpage = '<a class="btn btn-default btn-xs" href="catalog.php?page=1'.$clink.$flink.'" role="button"><<</a> <a class="btn btn-default btn-xs" href="catalog.php?page='.($page-1).''.$clink.$flink.'" role="button"><</a>';
// Проверяем нужны ли стрелки вперед
if ($page != $total) $nextpage = '<a class="btn btn-default btn-xs" href="catalog.php?page='.($page+1).''.$clink.$flink.'" role="button">></a> <a class="btn btn-default btn-xs" href=catalog.php?page='.$total.''.$clink.$flink.' role="button">>></a>';
// Находим две ближайшие станицы с обоих краев, если они есть
if($page - 5 > 0) $page5left = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page - 5) .''.$clink.$flink.' role="button">'. ($page - 5) .'</a> ';
if($page - 4 > 0) $page4left = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page - 4) .''.$clink.$flink.' role="button">'. ($page - 4) .'</a> ';
if($page - 3 > 0) $page3left = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page - 3) .''.$clink.$flink.' role="button">'. ($page - 3) .'</a> ';
if($page - 2 > 0) $page2left = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page - 2) .''.$clink.$flink.' role="button">'. ($page - 2) .'</a> ';
if($page - 1 > 0) $page1left = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page - 1) .''.$clink.$flink.' role="button">'. ($page - 1) .'</a> ';
if($page + 5 <= $total) $page5right = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page + 5) .''.$clink.$flink.' role="button">'. ($page + 5) .'</a> ';
if($page + 4 <= $total) $page4right = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page + 4) .''.$clink.$flink.' role="button">'. ($page + 4) .'</a> ';
if($page + 3 <= $total) $page3right = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page + 3) .''.$clink.$flink.' role="button">'. ($page + 3) .'</a> ';
if($page + 2 <= $total) $page2right = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page + 2) .''.$clink.$flink.' role="button">'. ($page + 2) .'</a> ';
if($page + 1 <= $total) $page1right = ' <a class="btn btn-default btn-xs" href=catalog.php?page='. ($page + 1) .''.$clink.$flink.' role="button">'. ($page + 1) .'</a> ';
// Вывод меню если страниц больше одной
if ($total > 1)
{
	echo $pervpage.$page5left.$page4left.$page3left.$page2left.$page1left.'<a class="btn btn-success btn-xs disabled" role="button"><b>'.$page.'</b></a>'.$page1right.$page2right.$page3right.$page4right.$page5right.$nextpage;
}	
echo <<<HTML
		</div>
			
		</div>
		
		
	
HTML;
// конец постраничной навигации      
        
include('tpl/footer.php');

mysqli_close($link);

?>
