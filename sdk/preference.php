<?php

//Pagamento por Preference (Link de Pagamento) - SDK MP



//Importando arquivo que guarda o accesstoken do Mercado Pago e colocando em uma variável
$config = require_once("../config.php");

//Importando os arquivos necessários da pasta LIB/Vendor (Biblioteca SDK - Composer do MP)
require_once("lib/vendor/autoload.php");

//Importando a classe de Conexão
require_once("../class/Conn.class.php");

//Importando a classe de Pagamentos
require_once("../class/Payment.class.php");




//Importações das Classes Necessárias (classes do Composer - Vendor)

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;


$accessTokenProduction = $config['accesstoken_production'];

$accessTokenTest = $config['accesstoken_test'];

MercadoPagoConfig::setAccessToken($accessTokenProduction); //AccessToken de Produção

MercadoPagoConfig::setAccessToken($accessTokenTest); //AccessToken de Teste



//Captura o parâmetro Token(que está vindo no corpo da requisição)

$content = file_get_contents("php://input");
$body = json_decode($content);


//Verifica se o parâmetro token existe 

//OBS: objeto do tipo stdClass, tem seus valores acessados como uma class
if (!isset($body->token)) {
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

            if ($_GET['vl'] < 1 || $_GET['vl'] > 100) {
                die('Valor Deve Ser Positivo e menor que R$ 100.00!');
            }
        }
    }


    // echo "AMOUNT" . $amount;


    //Passou por todas as validações acima e Captura o Valor
    $amount = (float) trim($_GET['vl']);
    // var_dump(is_numeric(trim($amount)));

    //Istanciando o objeto de Payment
    $payment = new Payment(1);

    //Chamando o método de Adicionar o Pagamento passando o valor do pagamento ($amount) como Parâmetro
    $payCreate = $payment->addPayment($amount);

    //Verifica se a inserção do Pagamento deu certo com o $payCreate

    if ($payCreate) {

        $client = new PreferenceClient();

        //Array com a Requesição Necessária

        $createRequest = [
            "external_reference" => $payCreate,
            "notification_url" => $config['url_notification_sdk'],
            "items" => array(
                array(
                    "id" => "4567",
                    "title" => "Dummy Title",
                    "description" => "Dummy description",
                    "picture_url" => "http://www.myapp.com/myimage.jpg",
                    "category_id" => "eletronico",
                    "quantity" => 1,
                    "currency_id" => "BRL",
                    "unit_price" => $amount
                )
            ),
            "default_payment_method_id" => "master",
            "excluded_payment_types" => array(
                array(
                    "id" => "ticket"
                )
            )
        ];

        //Variável (Objeto) que realizará a Requisição / Criação da Preference (Link Externo de Pagamento)

        $preference = $client->create($createRequest);

        //Se o atributo ID estiver presente e for diferente de nulo
        if (isset($preference->id) && $preference->id !== null) {

            if (isset($card)) {
                $preference_id = $preference->id;
            } else {
                //Campo que guarda o link de pagamento externo / ticket
                $link_externo = $preference->init_point;

                //Campo referente ao ID do pagamento (que será armazenado no BD)
                $external_reference = $preference->external_reference;

                //Variável com o código HTML que envolverá os parâmetros de pagamento

                $html = "
                        <h3>{$amount} #{$external_reference}</h3>
                        <a href='{$link_externo}' target='_blank'>Link Externo</a>
                    ";

                echo $html;

                // echo $copia_e_cola;
            }

        }


    }

}

?>