<?php

use MercadoPago\Client\Payment\PaymentClient;

$card = true;

//Reaproveitando o arquivo de preference
require_once("preference.php");

//OBS: objeto do tipo stdClass, tem seus valores acessados como uma class
if (isset($body->token)) {


    $client = new PaymentClient();

    //Array com a Requesição Necessária

    $createRequest = [
        "transaction_amount" => $body->transaction_amount,
        "description" => "description",
        "issuer_id" => $body->issuer_id,
        "token" => $body->token,
        "installments" => $body->installments,
        "payment_method_id" => $body->payment_method_id,
        "payer" => [
            "email" => $body->payer->email,
            "identification" => [
                "type" => $body->payer->identification->type,
                "number" => $body->payer->identification->number,
            ],
        ],
    ];

    $payment = $client->create($createRequest);

    echo json_encode($payment);

    die;

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/style.css">

    <title>Cartão</title>

    <script src="https://sdk.mercadopago.com/js/v2"></script>


</head>

<body>

    <input type="hidden" id="valor_payment" value="<?= $amount; ?>">
    <input type="hidden" id="preference_id" value="<?= $preference_id; ?>">


    <div class="card-page">
        <div id="statusScreenBrick_container"></div>
        <div id="paymentBrick_container"></div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/card.js"></script>

</body>

</html>