<?php
/*
 * Example 4 - How to prepare an iDEAL payment with the Mollie API.
 */
//if(isset($_POST["booking_submit"])){
//echo 'hitted';
//echo dirname(__FILE__);
//die();
//print_r($_POST);
if (isset($_POST['membership_submit'])) {
    $user_id = get_current_user_id();
}
//die();
try {

    require_once dirname(__FILE__) . "/src/Mollie/API/Autoloader.php";

    $mollie = new Mollie_API_Client;

    //$mollie->setApiKey("test_GJUFdmDA6N2EVAed747JQD9r94RBVm");

    $mollie->setApiKey("test_SMjPqNvsrxp6H7gnDBQ7uNHH2Kcm8v");

    /*
     * First, let the customer pick the bank in a simple HTML form. This step is actually optional.
     */
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $issuers = $mollie->issuers->all();

        echo '<form method="post">Select your bank: <select name="issuer">';

        foreach ($issuers as $issuer) {
            if ($issuer->method == Mollie_API_Object_Method::IDEAL) {
                echo '<option value=' . htmlspecialchars($issuer->id) . '>' . htmlspecialchars($issuer->name) . '</option>';
            }
        }

        echo '<option value="">or select later</option>';
        echo '</select><button>OK</button></form>';
        exit;
    }

    /*
     * Generate a unique order id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $order_id = time();

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

    global $wpdb;

    $total_cost = $_POST['membership_plan'];

    $payment = $mollie->payments->create(array(
        "amount"      => $total_cost,
        "method"      => Mollie_API_Object_Method::IDEAL,
        "description" => "Your Bookings",
        "webhookUrl"  => "{$protocol}://{$hostname}/booking-process/",
        "redirectUrl" => "{$protocol}://{$hostname}{$path}/thank-you?order_id={$order_id}&user_id={$user_id}",
        "metadata"    => array(
            "order_id" => $order_id,
        ),
        "issuer"      => !empty($_POST["issuer"]) ? $_POST["issuer"] : null,
    ));

    //echo $payment->id;
    $payment = $mollie->payments->get($payment->id);
    //print_r($payment);
    //echo 'Umakant';
    //echo $payment->status;

    //echo $payment->isPaid();
    //die();
    if ($payment->status == 'paid') {
        //echo 'STATUS PAID';
        //database_write($order_id, $payment->status, $user_id, $total_cost);
    }

    database_write($order_id, $payment->status, $user_id, $total_cost);
    //die();

    /*
     * Send the customer off to complete the payment.
     */
    //header("Location: " . $payment->getPaymentUrl());
    ?>
  <script>
    window.location.href = '<?php echo $payment->getPaymentUrl(); ?>';
  </script>

  <?php
} catch (Mollie_API_Exception $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}

/*
 * NOTE: This example uses a text file as a database. Please use a real database like MySQL in production code.
 */

function database_write($order_id, $status, $user_id, $total_cost)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "registration_membership";

    /*echo '<pre>';
    print_r($_POST);
    echo '</pre>';*/
    //echo sizeof($_POST);
    //die();
    //echo 'Please Wait.. Redirecting to Payment Page....';

    //$order_id = intval($order_id);

    // echo "The number is: $x <br>";
    //echo 'Umakatn';
    //echo $status;
    //$total_cost = 10;
    //die();

    $extend_membership = $_POST['extend_membership'];

    $date = strtotime($_POST['registeration_time']);

    if ($total_cost == 6) {
        $new_date = strtotime('+ 6 month', $date);
    } else {
        $new_date = strtotime('+ 1 year', $date);
    }

    $date_new = date('Y-m-d H:i:s', $new_date);
    
    if ($extend_membership) {

        $user_id             = get_current_user_id();
        $membership_order_id = get_user_meta($user_id, 'membership_order_id', true);

        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}registration_membership WHERE user_id = $user_id", OBJECT);

        $expire_date = $results[0]->expire_time;
        $order_id    = $results[0]->order_id;
        $userid      = $results[0]->user_id;

        $extend_expire_date = strtotime($expire_date);

        
        if ($total_cost == 6) {
            $final_expire_date = strtotime('+ 6 month', $extend_expire_date);
        } else {
            $final_expire_date = strtotime('+ 1 year', $extend_expire_date);
        }

       //$final_expire_date = strtotime('+ 1 year', $extend_expire_date);

        $expire_date_last   = date('Y-m-d H:i:s', $final_expire_date);

        if (($membership_order_id == $order_id) && ($user_id == $userid)) {
            //die();
            $wpdb->update(
                'wp_registration_membership',
                array(
                    'expire_time' => $expire_date_last, // string
                    'order_price'  => $total_cost
                ),
                array(
                  'user_id' => $userid, 
                  'order_id' => $order_id
                ),
                array(
                    '%s', // value2
                    '%d'
                ),
                array(
                  '%d', 
                  '%d'
                )
            );
            //echo $wpdb->last_query;
        }
    } else {
        $query = $wpdb->insert(
            $table_name, array(
                'user_id'      => $user_id,
                'payment_time' => $_POST['registeration_time'],
                'status'       => $status,
                'order_id'     => $order_id,
                'order_price'  => $total_cost,
                'expire_time'  => $date_new,
            ), array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
            )
        );
    }
}

//}