<?php
function exec_ogp_module()
{	
global $db,$view,$settings;
$loadpage = "?m=billing&p=paid";
$count = $_POST['count'] + 1;

$result = $db->resultquery("SELECT * from OGP_DB_PREFIXbilling_carts WHERE cart_id= '". $_POST['cart_id'] . "'");
foreach($result as $cartID){
	 $paid = $cartID['paid'];
	}

echo "<h2>Processing your Payment Info ... </h2>";
if($settings['debug']==1){
echo "<br>";
echo $_POST['count'];
echo "<br>";
echo $_POST['cart_id'];
echo "<br>";
echo $_POST['payment_status'];
echo "<br>";
}
//check the DB and see if its been updated as paid
if($paid > 0){
	$loadpage = "?m=billing&p=create_servers";

	}

//waited too long .. go to orders page
if($count > 5){
	$loadpage = "?m=billing&p=orders";
	echo "<h2>There was a Problem, Please contact Support ... </h2>";

}
?>


<form name='paid' action='<?php echo  $loadpage?>' method='post'>
<input type='hidden' name='cart_id' value='<?php echo $_POST["cart_id"]?>'>
<input type='hidden' name='payment_status' value='<?php echo $_POST["payment_status"] ?>'>
<input type='hidden' name='count' value='<?php echo $count?>'>
</form>
  <script>
    var auto_refresh = setInterval(
    function()
    {
    submitform();
    }, 5000);
    function submitform()
    {
      document.paid.submit();
    }
    </script>	
<?php	
}
?>


