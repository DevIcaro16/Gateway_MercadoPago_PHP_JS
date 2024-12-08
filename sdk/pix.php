<?php

// var_dump($_GET['vl']);


//Pagamento por PIX - SDK ()


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


//Importações

$config = require_once("../config.php");



//Importando os arquivos necessários da pasta LIB/Vendor (Biblioteca SDK - Composer do MP)
require_once("lib/vendor/autoload.php");

//Importando a classe de Conexão
require_once("../class/Conn.class.php");

//Importando a classe de Pagamentos
require_once("../class/Payment.class.php");


//Importações das Classes Necessárias (classes do Composer - Vendor)

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

//Passou por todas as validações acima e Captura o Valor
$amount = (float) trim($_GET['vl']);

//Istanciando o objeto de Payment
$payment = new Payment(1);

//Chamando o método de Adicionar o Pagamento passando o valor do pagamento ($amount) como Parâmetro
$payCreate = $payment->addPayment($amount);

//Verifica se a inserção do Pagamento deu certo com o $payCreate

if ($payCreate) {

    $accessTokenProduction = $config['accesstoken_production'];

    // Setando / Modificando o accesstoken do mp atráves da classe MercadoPagoConfig

    MercadoPagoConfig::setAccessToken($accessTokenProduction); //AccessToken de Produção

    //Objeto client -> Classe PaymentClient
    $client = new PaymentClient();

    //Array com a Requesição Necessária

    $createRequest = [
        "transaction_amount" => $amount,
        "description" => "description",
        "external_reference" => $payCreate,
        "notification_url" => $config['url_notification_sdk'],
        "payment_method_id" => "pix",
        "payer" => [
            "email" => "cliente-email@gmail.com",
        ],
    ];

    //Variável (Objeto) que realizará a Requisição / Criação do Payment (Pagamento)

    $payment = $client->create($createRequest);

    //Se o atributo ID estiver presente e for diferente de nulo
    if (isset($payment->id) && $payment->id !== null) {

        // var_dump($payment);
        //Campo que guarda o código do qr_code
        $copia_e_cola = $payment->point_of_interaction->transaction_data->qr_code;

        //Campo que guarda a img do qr_code
        $img_qr_code = $payment->point_of_interaction->transaction_data->qr_code_base64;

        //Campo que guarda o link de pagamento externo / ticket
        $link_externo = $payment->point_of_interaction->transaction_data->ticket_url;

        //Campo que guarda o valor da transação PIX
        $transaction_amount = $payment->transaction_amount;

        //Campo referente ao ID do pagamento (que será armazenado no BD)
        $external_reference = $payment->external_reference;

        //Variável com o código HTML que envolverá os parâmetros de pagamento

        $html = "
                <h3>{$transaction_amount} #{$external_reference}</h3>
                <img src='data:image/png;base64, {$img_qr_code}' alt='' width='200'> <br/>
                <h3>{$config['url_notification_sdk']}</h3>
                <textarea>{$copia_e_cola}</textarea> <br/>
                <a href='{$link_externo}' target='_blank'>Link do Pagamento</a>
            ";

        echo $html;

        // echo $copia_e_cola;
    }

}

?>