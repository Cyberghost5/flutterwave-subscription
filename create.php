<?php
session_start();
include 'settings.php';

if(isset($_POST['termsAgreement'])){

// Variables submitted from the form
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phonenumber = $_POST['phonenumber'];
$plan_details = $_POST['plan_details'];

$plan_details_explode = explode('|', $plan_details);
$plan_id = $plan_details_explode[0];
$plan_amount = $plan_details_explode[1];
$plan_currency = $plan_details_explode[2];

$subscription_data = array(
    "tx_ref" => uniqid("txref_"),
    "order_id" => uniqid("order_"),
    "currency" => $plan_currency, 
    "amount" => $plan_amount,
    "redirect_url" => "http://localhost/flutterwave-subscription/process.php", //$returnflutter,
    "customer" => array(
        "email" => $email,
        "name" => $fullname,
        "phone" => $phonenumber,
    ),
    "meta" => array(
        "payment_plan" => $plan_id,
        "price" => $plan_amount,
    ),
    "customizations" => array(
        "title" => "Flutterwave Subscription",
        "description" => "Payment for FT Sub",
        "logo" => "http://localhost/flutterwave-subscription/assets/images/icon_branding.svg"
    ),
    "payment_plan" => $plan_id,
);

// echo '<pre>';
// var_dump($subscription_data);
// echo '</pre><br>';
// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';

// Set up the cURL request to initiate a subscription
$ch = curl_init($subscription_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($subscription_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $secret_key,
    "Content-Type: application/json",
));

// Execute the cURL request to initiate a subscription
$response = curl_exec($ch);

$res = json_decode($response);
// echo '<pre>';
// print_r($response);
// echo '</pre>';

// Check for errors
if (curl_errno($ch)) {
    $_SESSION['error'] = 'Error: ' . curl_error($ch);
    echo "<script>window.location.assign('index.php')</script>";
}

// Close cURL resource
curl_close($ch);

// Handle the response for initiating a subscription
$subscription_response_data = json_decode($response, true);
// echo '<pre>';
// print_r($subscription_response_data);
// echo '</pre>';

// Redirect the customer to the payment link
$return_url = $subscription_response_data["data"]["link"];

if ($return_url == '') {
    $_SESSION['error'] = "The URL was not generated";
	echo "<script>window.location.assign('index.php')</script>";
}
else{
    echo "<script>window.location.assign('$return_url')</script>";
}
}
else{
	$_SESSION['error'] = 'No shortcuts, Fill up the form first';
	echo "<script>window.location.assign('index.php')</script>";
}
?>