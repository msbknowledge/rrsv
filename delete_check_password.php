<button onclick="window.history.back()">back</button>
<form id="myForm" method="post" action="add_bill_post_delete.php">
    <input type="hidden" name="recep_no" value="<?=$_GET['recep_no'];?>">
Enter password :<input type="password" name="password">
<input type="submit"/> 
</form>
  <button onclick="document.getElementById('myForm').reset()">reset</button>
