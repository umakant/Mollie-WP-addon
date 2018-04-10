<?php
/*
 * Example 3 - How to show a return page to the customer.
 *
 * In this example we retrieve the order stored in the database.
 * Here, it's unnecessary to use the Mollie API Client.
 */
	//print_r($_POST);
/*
	 * Initialize the Mollie API library with your API key.
	 *
	 * See: https://www.mollie.com/beheer/account/profielen/
	 */
	//include dirname(__FILE__)."/initialize.php";

	/*
	 * Retrieve the payment's current state.
	 */
	//echo $payment  = $mollie->payments->get($_POST["id"]);
	//echo $order_id = $payment->metadata->order_id;

//echo dirname(__FILE__)."/../functions/thirdparty/ideal/initialize.php"; 

$status = database_read($_GET["order_id"]);
//echo "<p>Your payment status is '" . htmlspecialchars($status) . "'.</p>";

if($status=='paid'){
	echo "Congratulation, Your bookings have been booked.";
}
elseif($status=='cancelled'){
echo "Oops, Your booking have been cancelled, Please try again after some time.";	
}
else {
echo "Oops, There is some problem, Please try again after some time.";		
}
/*
 * Determine the url parts to these example files.

$protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
$hostname = $_SERVER['HTTP_HOST'];
$path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

echo "<p>Your payment status is '" . htmlspecialchars($status) . "'.</p>";
echo "<p>";
echo '<a href="' . $protocol . '://' . $hostname . $path . '/01-new-payment.php">Retry example 1</a><br>';
echo '<a href="' . $protocol . '://' . $hostname . $path . '/04-ideal-payment.php">Retry example 4</a><br>';
echo '<a href="' . $protocol . '://' . $hostname . $path . '/10-oauth-new-payment.php">Retry example 10</a><br>';
echo '<a href="' . $protocol . '://' . $hostname . $path . '/12-new-customer-payment.php">Retry example 12</a><br>';
echo '<a href="' . $protocol . '://' . $hostname . $path . '/14-recurring-first-payment.php">Retry example 14</a><br>';
echo "</p>";

 */
/*
 * NOTE: This example uses a text file as a database. Please use a real database like MySQL in production code.
 */
function database_read ($order_id)
{
global $wpdb;

$table_name  = $wpdb->prefix . "client_orders";

    //print_r($_POST);
	$order_id = intval($order_id);
    $orders = $wpdb->get_results( "SELECT * FROM $table_name where odb_order_id= $order_id AND user_id=". get_current_user_id());

	$status = $orders[0]->odb_bkg_status;
	
	//$database = dirname(__FILE__) . "/orders/order-{$order_id}.txt";

	//$status  = @file_get_contents($database);

	return $status ? $status : "unknown order";
}
