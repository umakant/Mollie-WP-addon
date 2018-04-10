<?php 

try
{
  /*
   * Initialize the Mollie API library with your API key.
   *
   * See: https://www.mollie.com/beheer/account/profielen/
   */
  include dirname(__FILE__)."/initialize.php";
  //include dirname(__FILE__)."/../functions/thirdparty/ideal/initialize.php"; 
   //require_once(dirname(__FILE__)."/../functions/thirdparty/ideal/webhook-verification.php"); 
  

  /*
   * Retrieve the payment's current state.
   */
  //$payment  = $mollie->payments->get($_POST["id"]);
  
global $wpdb;
 
  $payment = $mollie->payments->get($_POST['id']);

  $order_id = $payment->metadata->order_id;


$wpdb->delete( $cart_table, array(  'odb_order_id' => $order_id), array( '%s' ) );


    //database_write($order_id, $payment->status, $user_id, $total_cost);

  if ($payment->isPaid())
  {


  /*
   * Update the order in the database.
   */
  // database_write($order_id, $payment->status);





    /*
     * At this point you'd probably want to start the process of delivering the product to the customer.
     */
  }
  elseif ($payment->isOpen() == FALSE)
  {
     





    /*
     * The payment isn't paid and isn't open anymore. We can assume it was aborted.
     */
  }
}
catch (Mollie_API_Exception $e)
{
  echo "API call failed: " . htmlspecialchars($e->getMessage());
  print_r( $_POST);
  //echo '<br>';
  //echo $order_id;

}


