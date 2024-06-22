<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Flutterwave Subscription - PHP API</title>
  <link href="https://fonts.googleapis.com/css?family=Karla:400,700&amp;display=swap" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/bd-wizard.css">
</head>
<?php
include 'settings.php';

// Fetch plans from Flutterwave API
$ch = curl_init($plan_url_check);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $secret_key,
    "Content-Type: application/json",
));

$response = curl_exec($ch);

$res = json_decode($response);
// echo '<pre>';
// print_r($response);
// echo '</pre>';

// Check for errors
if (curl_errno($ch)) {
    $_SESSION['error'] = 'Error: ' . curl_error($ch);
    // exit();
}

// Close cURL resource
curl_close($ch);

// Handle the response for fetching plans
$plans_data = json_decode($response, true);
// echo '<pre>';
// var_dump($response);
// echo '</pre>';

// Check if plans are successfully fetched
if (!$plans_data || !isset($plans_data['data'])) {
    $_SESSION['error'] = 'Error: Unable to fetch plans.';
    // exit();
}
?>
<body class="min-vh-100">
  <header>
    <div class="brand-wrapper">
      <img src="assets/images/logo.svg" alt="logo" class="logo">
    </div>
  </header>
  <main class="px-4 pb-4">
    <div class="card bd-wizard-card">
      <div class="card-body">
            <div id="wizard">
              <h3>Plans</h3>
              <section>
                <h4 class="section-heading">Subscribed Plans.</h4>
                <div class="container parent form-group">
                    <div class="row">
                        <?php foreach ($plans_data['data'] as $plan) : ?>
                        <div class='col col-3 text-center'>
                            <input type="radio" name="plan_details" id="<?= $plan['id']; ?>" class="d-none imgbgchk" value="<?= $plan['id']; ?>|<?= $plan['amount']; ?>|<?= $plan['currency']; ?>" required>
                            <label for="<?= $plan['id']; ?>">
                              <img src="assets/images/icon_branding.svg" alt="Image <?= $plan['id']; ?>">
                               <div class="tick_container">
                                  <div class="tick">
                                      <i class="fa fa-check"></i>
                                  </div>
                              </div>
                            </label>
                            <p><?= $plan['name'] . ' (' . $plan['id'] . ')' . ' - ' . ($plan['currency']) . '' . $plan['amount']. ' | Status - '.$plan['status']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
              </section>
            </div>
      </div>
    </div>
  </main>
  <script src="assets/js/jquery-3.4.1.min.js"></script>
  <script src="assets/js/jquery.validate.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/jquery.steps.min.js"></script>
  <script src="assets/js/bd-wizard.js"></script>
</body>
</html>