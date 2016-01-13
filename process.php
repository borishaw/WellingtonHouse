<?php
//Delay response for n seconds
sleep(2);

$json_response = ['success' => 0, 'message' => ''];

//Throw an error
//http_response_code(500);

//Get data from AJAX
$formData = json_decode(file_get_contents('php://input'), true);

//Format Form Data to one single array
$formDataArray = array();
foreach ($formData as $data){
    $formDataArray[$data['name']] = $data['value'];
}
$email = $formDataArray['Email'];
$phone = $formDataArray['Phone'];

//Load libraries
require_once __DIR__ . '/vendor/autoload.php';

//ReCaptcha and Validtor
use Respect\Validation\Validator as v;
if (!v::email()->validate($email)){
    $json_response['message'] = 'Invalid Email Address';
    die(json_encode($json_response));
}

if (!v::phone()->validate($phone)){
    $json_response['message'] = 'Invalid Phone Number';
    die(json_encode($json_response));
}

$recaptcha = new \ReCaptcha\ReCaptcha('6LdGKBUTAAAAABbnSImOug65r_8-dYzneRNx8mxj');
$resp = $recaptcha->verify($formDataArray['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
if ($resp->isSuccess()) {
    unset($formDataArray['g-recaptcha-response']);
    $mail = new PHPMailer();
    $mail->setFrom($formDataArray['Email'], $formDataArray['Name']);
    $mail->addAddress('boris@ankitdesigns.com', 'Boris');
    $mail->isHTML(true);
    $mail->Subject = 'New Registration';

    $mail->Body = '';
    foreach($formDataArray as $key => $value){
        $mail->Body .= $key . ': ' . $value. '<br/>';
    }
    if(!$mail->send()) {
        echo json_encode(['success' => 0, 'message' => 'Message could not be sent.' . ' Mailer Error: ' . $mail->ErrorInfo]);
        die;
    } else {
        echo json_encode(['success' => 1, 'message' => 'Message has been sent']);
        exit;
    }
} else {
    $errors = $resp->getErrorCodes();
    echo json_encode(['success' => 0, 'message' => 'ReCaptcha Failed. Error Code: '. print_r($errors)]);
    die;
}