<?php 
session_start();
include 'settings.php';
    if(isset($_GET['status'])){
        //* check payment status
        if($_GET['status'] == 'cancelled'){
            $_SESSION['cancelled'] = 'Why you cancel this payment na?';
            echo "<script>window.location.assign('index.php')</script>";
        }
        elseif($_GET['status'] == 'successful' || $_GET['status'] == 'completed'){
            $txid = $_GET['transaction_id'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txid}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                  "Authorization: Bearer " . $secret_key,
                  "Content-Type: application/json"
                ),
              ));
              
              $response = curl_exec($curl);
              
              curl_close($curl);
              
              $res = json_decode($response);
            //   echo '<pre>';
            //   print_r($response);
            //   echo '</pre>';
              if($res->status){
                $amountPaid = $res->data->charged_amount;
                $amountToPay = $res->data->meta->price;
                if($amountPaid >= $amountToPay){
                    // echo 'Payment successful</b>';

                    $plan_id = $res->data->plan;
                    $customer_email = $res->data->customer->email;
                    $customer_id = $res->data->customer->id;
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.flutterwave.com/v3/subscriptions?email={$customer_email}&transaction_id={$txid}&plan={$plan_id}&status=active",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer " . $secret_key,
                        "Content-Type: application/json"
                        ),
                    ));
                    
                    $response = curl_exec($curl);
                    
                    curl_close($curl);
                    
                    $res = json_decode($response);
                    $subscription_id = $res->data[0]->id;
                    if($res->meta->page_info->total == 1){
                        $_SESSION['success'] = "Payment Successful and Subscription is now active, check your email to learn more";
                        echo "<script>window.location.assign('index.php')</script>";
                    }elseif($res->meta->page_info->total > 1){
                        $_SESSION['cancelled'] = 'Payment Successful and Subscription was more than 1';
                        echo "<script>window.location.assign('index.php')</script>";
                    }else{
                        $_SESSION['cancelled'] = 'Payment Successful and Subscription was not found';
                        echo "<script>window.location.assign('index.php')</script>";
                    }
                    // echo '<pre>';
                    // print_r($response);
                    // echo '</pre>';
                    // echo 'Sub ID - '.$res->data[0]->id;

                    //* Continue to give item to the user
                }else
                {
                    $_SESSION['error'] = 'Fraud transaction detected';
                    echo "<script>window.location.assign('index.php')</script>";
                }
              }else
              {
                  $_SESSION['error'] = 'Can not process payment';
                  echo "<script>window.location.assign('index.php')</script>";
              }
        }
    }else{
        $_SESSION['error'] = 'Omo, e just get as e be';
        echo "<script>window.location.assign('index.php')</script>";
    }
// echo $customer_email;
?>