<?php
Error_reporting(E_ALL);
$link = mysqli_connect("localhost", "root", "", "exploit");
if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}

include ('acheck.php'); // проверка на логин
if(defined("IS_LOGIN")!=true or IS_LOGIN!="yes"){ header("Location: index.php"); exit();}


if(isset($_POST['ctitle'])) {
 if (mysqli_query($link, "INSERT INTO `category` (ctitle) VALUES ('".$_POST['ctitle']."')") === TRUE) {
    //printf("Данные успешно записаны.\n");
     $status="alert-success";
 } 
}
//printf("Errormessage: %s\n", mysqli_error($link));

//удаление категорий
if(isset($_GET['с_delete'])){
   mysqli_query($link, "DELETE FROM `category` WHERE id='".intval($_GET['с_delete'])."'" );	
   
}
// конец удаление категорий

include('tpl/header.php');
?>

<?php
        if(isset($status)) {        
?>
<div class="alert <?php echo $status ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php
        if($status=='alert-success') {        
?>
<strong>Категория успешно сохранена!</strong> 
<?php }?>
</div>
<?php }?>
      <h2>Добавление Категории</h2><br><br><br>
      <p><form class="form-horizontal" method="POST" action="add_category.php">
  <div class="control-group">
    <label class="control-label" for="ctitle">Название категории</label>
    <div class="controls">
      <input type="text" id="ctitle" placeholder="Название категории" class="input-xxlarge" name="ctitle">
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary">Отправить</button>
    </div>
  </div>
</form></p>
<p>
    <h2>Текущие категории</h2>
    <table class="table table-striped table-bordered" style="width:300px">
      <tr>
          <th>id</th>
          <th>Название</th>
          <td>Del</td>
      </tr>
<?php
$result = mysqli_query($link,"SELECT * FROM `category` ORDER BY `id` DESC");

while($tab_row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>".$tab_row['id']."</td><td>".$tab_row['ctitle']."</td><td><span class=\"badge badge-important\"><a href=\"add_category.php?с_delete=".$tab_row['id']."\">del</a></span></td></tr>";    
}
?>
    </table>
    
</p>

<?php
include('tpl/footer.php');
mysqli_close($link);
?>