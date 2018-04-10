<?php

require_once dirname(__FILE__) . "/src/Mollie/API/Autoloader.php";

/*
 * Initialize the Mollie API library with your API key.
 *
 * See: https://www.mollie.com/beheer/account/profielen/
 */
$mollie = new Mollie_API_Client;

$mollie->setApiKey("test_SMjPqNvsrxp6H7gnDBQ7uNHH2Kcm8v");

//$mollie_api = esc_attr(get_option('odb_mollie_api'));
//$mollie->setApiKey("$mollie_api");