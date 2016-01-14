<?php
//Delay response for n seconds
sleep(2);

//Redirect to index.html if the request method is not POST
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    header('location: index.html');
    die;
}


$json_response = ['success' => 0, 'message' => ''];

//Throw an error
//http_response_code(500);

//Get data from AJAX
$formData = json_decode(file_get_contents('php://input'), true);

//Format Form Data to one single array
$formDataArray = array();
foreach ($formData as $data){
    $formDataArray[$data['name']] = htmlspecialchars($data['value']);
}
$email = $formDataArray['Email'];
$phone = $formDataArray['Phone'];

//Load PHP libraries
require_once __DIR__ . '/vendor/autoload.php';

//Validate User Input
use Respect\Validation\Validator as v;
foreach ($formDataArray as $data) {
    if (!v::notEmpty()->validate($data)){
        $json_response['message'] = 'Please enter all required fields';
        die(json_encode($json_response));
    }
}

if (!v::email()->validate($email)){
    $json_response['message'] = 'Invalid Email Address';
    die(json_encode($json_response));
}

if (!v::phone()->validate($phone)){
    $json_response['message'] = 'Invalid Phone Number';
    die(json_encode($json_response));
}

//Validate Google ReCaptcha
$recaptcha = new \ReCaptcha\ReCaptcha('6LdGKBUTAAAAABbnSImOug65r_8-dYzneRNx8mxj');
$resp = $recaptcha->verify($formDataArray['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
if ($resp->isSuccess()) {
    unset($formDataArray['g-recaptcha-response']);
    $mail = new PHPMailer();
    $mail->setFrom($formDataArray['Email'], $formDataArray['First Name'].' '.$formDataArray['Last Name']);
    $mail->addAddress('julie@ankitdesigns.com', 'Julie');
    $mail->isHTML(true);
    $mail->Subject = 'New Registration';

    $mail->Body = '';
    foreach($formDataArray as $key => $value){
        $mail->Body .= $key . ': ' . $value. '<br/>';
    }
    if(!$mail->send()) {
        $json_response['message'] = 'Message could not be sent.' . ' Please try again.' . ' Mailer Error: ' . $mail->ErrorInfo;
        die(json_encode($json_response));
    } else {
        $json_response['message'] = 'Message has been sent';
        $json_response['success'] = 1;
        exit(json_encode($json_response));
    }
} else {
    $errors = $resp->getErrorCodes();
    $json_response['message'] = 'ReCaptcha Failed. Error Code: '. $errors[0];
    die(json_encode($json_response));
}