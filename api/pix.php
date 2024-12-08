<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIX API </title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>

</body>

</html>
<?php

//Pagamento por PIX - API


//Verifica se o parâmetro vl (valor) existe 

if (!isset($_GET['vl'])) {
    //die -> Acaba com tudo que vem depois.
    die('Valor Não Existe!');
} else {

    //Verifica se o vl é nulo e ou se é um valro numérico
    if ($_GET['vl'] == "" || !is_numeric(trim($_GET["vl"]))) {
        die("Valor Não Pode Ser Vazio e Deve Ser Numérico!");
    } else {

        //Verifca se o valor é um valor válido (Positivo)

        if ($_GET['vl'] < 0.01 || $_GET['vl'] > 100) {
            die('Valor Deve Ser Positivo e menor que R$ 100.00!');
        }
    }
}


// echo "AMOUNT" . $amount;

//Importando arquivo que guarda o accesstoken do Mercado Pago e colocando em uma variável
$config = require_once("../config.php");

//Importando a classe de Conexão
require_once("../class/Conn.class.php");

//Importando a classe de Pagamentos
require_once("../class/Payment.class.php");

//Passou por todas as validações acima e Captura o Valor
$amount = (float) trim($_GET['vl']);

//Istanciando o objeto de Payment
$payment = new Payment(1);

//Chamando o método de Adicionar o Pagamento passando o valor do pagamento ($amount) como Parâmetro
$payCreate = $payment->addPayment($amount);

//Verifica se a inserção do Pagamento deu certo com o $payCreate

if ($payCreate) {

    $accessTokenProduction = $config['accesstoken_production'];


    //PHP - cURL necessária para uma transação PIX Mercado Pago (obtida pelo postman)

    $curl = curl_init();

    //Gera uma chave de idempotência única para cada requisição. Baseada no Timestamp
    $idempotencyKey = uniqid();

    //Variáveis necessárias nos parâmetros da requisição ao mercado pago

    //1. Id do pagamento gerado pela aplicação -> $payCreate
    //2. Valor do Pagamento gerado pela aplicação-> $amount
    //3. chave de idempotência única para cada requisição. Baseada no Timestamp -> $idempotencyKey
    //4. token obtido nas configurações de desenvolvedor do MP -> $accessToken

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
        "description": "Payment for product",
        "external_reference": "' . $payCreate . '",         
        "notification_url": "' . $config['url_notification_api'] . '",
        "payer": {
            "email": "icaroip15@gmail.com",
            "identification": {
                "type": "CPF",
                "number": "62404879375"
            }
        },
        "payment_method_id": "pix",
        "transaction_amount": ' . $amount . '
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "X-Idempotency-Key: {$idempotencyKey}",
            'Authorization: Bearer ' . $accessTokenProduction
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    //Resposta
    // echo $response;

    //Decodificando a resposta JSON e armazenando em uma variável 
    $obj = json_decode($response, true);

    // var_dump($obj);

    //Se o atributo ID estiver presente e for diferente de nulo
    if (isset($obj['id']) && $obj['id'] !== null) {

        //Campo que guarda o código do qr_code
        $copia_e_cola = $obj['point_of_interaction']['transaction_data']['qr_code'];

        //Campo que guarda a img do qr_code
        $img_qr_code = $obj['point_of_interaction']['transaction_data']['qr_code_base64'];

        //Campo que guarda o link de pagamento externo / ticket
        $link_externo = $obj['point_of_interaction']['transaction_data']['ticket_url'];

        //Campo que guarda o valor da transação PIX
        $transaction_amount = $obj['transaction_amount'];

        //Campo referente ao ID do pagamento (que será armazenado no BD)
        $external_reference = $obj['external_reference'];

        // var_dump($obj['external_reference']);

        //Variável com o código HTML que envolverá os parâmetros de pagamento

        $html = "
                <div class='main-container'>
                    <div class='content-container'>
                        <h2>API</h2> 
                        <hr>
                        <div class='h3-container'>
                            <h3 id='h3-valor'>Valor: R$ {$transaction_amount}</h3>
                            <h3 id='h3-id'>ID: #{$external_reference}</h3>
                        </div>
                        <label>QR Code</label>
                        <img src='data:image/png;base64, {$img_qr_code}' alt='' width='200'> <br/>
                        <label>Copia e Cola</label>
                        <textarea>{$copia_e_cola}</textarea> <br/>
                        <a href='{$link_externo}' target='_blank'>Link do Pagamento</a>
                        </div>
                </div>
            ";

        echo $html;

        // echo $copia_e_cola;
    }
}



// var_dump($config);
?>