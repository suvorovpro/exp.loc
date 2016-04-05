<?php
Error_reporting(E_ALL);
$link = mysqli_connect("localhost", "root", "", "exploit");
if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}

include ('acheck.php'); // проверка на логин
if(defined("IS_LOGIN")!=true or IS_LOGIN!="yes"){ header("Location: index.php"); exit();}



if(isset($_POST['fid_category'])) {
 if (mysqli_query($link, "INSERT INTO `exploits` (id_category,title,full_text,cve,author,date_add,tags) VALUES ('".$_POST['fid_category']."','".$_POST['ftitle']."','".$_POST['ffull_text']."','".$_POST['fcve']."','".$_POST['fauthor']."',NOW(),'".$_POST['ftags']."')") === TRUE) {
    //printf("Данные успешно записаны.\n");
     $status="alert-success";
 } 
}
//printf("Errormessage: %s\n", mysqli_error($link));





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
<strong>Эксплоит успешно сохранен!</strong> 
<?php }?>
</div>
<?php }?>
      <h2>Добавление эксплоита</h2><br><br><br>
      <p><form class="form-horizontal" method="POST" action="add.php">
  <div class="control-group">
    <label class="control-label" for="fid_category">Категория</label>
    <div class="controls">
      <select id="fid_category" name="fid_category">
         
<?php
$result = mysqli_query($link,"SELECT * FROM `category` ORDER BY `id`");

while($tab_row = mysqli_fetch_assoc($result)) {
    echo "<option value=\"".$tab_row['id']."\">".$tab_row['ctitle']."</option>";    
}
?>

          
    </select>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="ftitle">Заголовок</label>
    <div class="controls">
      <input type="text" id="ftitle" placeholder="Заголовок" class="input-xxlarge" name="ftitle">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="ffull_text">Эксплоит</label>
    <div class="controls">
      
      <textarea rows="12" id="ffull_text" placeholder="Эксплоит" class="input-xxlarge" name="ffull_text"></textarea>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="fcve">CVE</label>
    <div class="controls">
      <input type="text" id="fcve" placeholder="CVE" class="input-xxlarge" name="fcve">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="fauthor">Автор</label>
    <div class="controls">
      <input type="text" id="fauthor" placeholder="Автор" class="input-xxlarge" name="fauthor">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="ftags">Тэги</label>
    <div class="controls">
      <input type="text" id="ftags" placeholder="Тэги" class="input-xxlarge" name="ftags">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary">Отправить</button>
    </div>
  </div>
</form></p>


<?php
include('tpl/footer.php');
mysqli_close($link);
?>