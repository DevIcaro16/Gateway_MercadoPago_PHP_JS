<?php

// Verifica se o parâmetro "vl" (valor) existe
// if (!isset($_GET['vl'])) {
//     die('Valor Não Existe!');
// }

// // Valida o valor fornecido
// $amount = trim($_GET['vl']);
// if ($amount === "" || !is_numeric($amount)) {
//     die("Valor Não Pode Ser Vazio e Deve Ser Numérico!");
// } elseif ($amount < 1 || $amount > 100) {
//     die('Valor Deve Ser Positivo e menor que R$ 100.00!');
// }

// Converte o valor para float
// $amount = (float)$amount;

// // Importa configurações e classes necessárias
// $config = require_once("../config.php");
// require_once("../class/Conn.class.php");
// require_once("../class/Payment.class.php");

// // Instancia a classe Payment e cria um pagamento
// $payment = new Payment(1);
// $payCreate = $payment->addPayment($amount);

// if (!$payCreate) {
//     die("Erro ao criar pagamento no banco de dados!");
// }

$card = true;

//Reaproveitando o arquivo de preference
require_once("preference.php");

//OBS: objeto do tipo stdClass, tem seus valores acessados como uma class
if(isset($body->token)){

    // var_dump($body);

    $idempotencyKey = uniqid();

    // Monta o payload de forma segura
    $payload = [
        "description" => "Payment for product",
        "installments" => 1,
        "payer" => [
            "first_name" => "",
            "last_name" => "",
            "email" => $body->payer->email ?? '',
            "identification" => [
                "type" => $body->payer->identification->type ?? '',
                "number" => $body->payer->identification->number ?? ''
            ]
        ],
        "issuer_id" => $body->issuer_id ?? '',
        "payment_method_id" => $body->payment_method_id ?? '',
        "token" => $body->token ?? '',
        "transaction_amount" => $body->transaction_amount
    ];

    // Inicializa o cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . $idempotencyKey,
            'Authorization: Bearer ' . $accessTokenTest
        ],
    ]);

    // Executa a requisição e captura a resposta
    $response = curl_exec($curl);

    curl_close($curl);

    echo $response;

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