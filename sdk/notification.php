<?php

//PHP que vai lidar com as notificações de pagamento


//IMPORTAÇÕES

$config = require_once("../config.php");
require_once("lib/vendor/autoload.php");

//Importando a classe de Conexão
require_once("../class/Conn.class.php");

//Importando a classe de Pagamentos
require_once("../class/Payment.class.php");

//Importando a classe de Usuários
require_once("../class/User.class.php");

//Importações das Classes Necessárias (classes do Composer - Vendor)

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

$accessTokenProduction = $config['accesstoken_production'];

// Setando / Modificando o accesstoken do mp atráves da classe MercadoPagoConfig

MercadoPagoConfig::setAccessToken($accessTokenProduction); //AccessToken de Produção

var_dump($body);

$content = file_get_contents("php://input");
$body = json_decode($content);


//Verifica se o pagamento foi feito (se consta o registro na tabela do BD)
if (isset($body->data->id)) {

    $id = $body->data->id;
    $client = new PaymentClient();

    $payment = $client->get($id);

    if (isset($payment->id)) {

        $payment_class = new Payment();
        $payment_class->payment_id = $payment->external_reference;
        $payment_data = $payment_class->get();

        //Se o registro de pagamento foi inserido na tabela do BD
        if ($payment_data) {

            //Verifica se o status do pagamento é aprovado / approved na tabela do BD
            if ($payment->status == "approved") {

                $user = new User($payment_data->user_id);

                //Adiciona no saldo do usuário
                $addBalance = $user->addBalance((float) $payment_data->valor);

                // header("Location: card.php");
            }

            // Seta/Modifica o status do pagamento
            $payment_class->setStatusPayment($payment->status);
        }


    }
}

?>