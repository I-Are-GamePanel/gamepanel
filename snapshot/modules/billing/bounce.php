<?php
 $url = "https://";
    // Append the host(domain name, ip) to the URL.   
    $url.= $_SERVER['HTTP_HOST'];   
// foreach($_POST as $key => $val) {
// echo 'Field name : ' . $key . ' Value :' .$val .'<br>';
// }

 if (($_POST['payment_status']=="Completed")){
	echo "<title>Success</title><h4>Thank you for your order. <br>  ... </h4><br>";
	echo "Processing your payment Information ..";
	$bounce_to = $url."/home.php?m=billing&p=paid";
} else {
	echo "<title>Uh OH</title><h4>There was a problem, Please contact Support<br>  ... </h4><br>";
	$bounce_to = $url."/home.php?m=billing&p=paid";
	//we can setup a "failed page" to redirect to. My sandbox payments are not marked completed for some reason

}
?>
<form name='paid' action='<?php echo  $bounce_to?>' method='post'>
<input type='hidden' name='cart_id' value='<?php echo $_POST["item_number"]?>'>
<input type='hidden' name='payment_status' value='<?php echo $_POST["payment_status"] ?>'>
</form>
  <script>
    var auto_refresh = setInterval(
    function()
    {
    submitform();
    }, 2000);
    function submitform()
    {
      document.paid.submit();
    }
    </script>







