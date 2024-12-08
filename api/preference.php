<?php

//Pagamento por Preference (Link de Pagamento) - API MP



//Importando arquivo que guarda o accesstoken do Mercado Pago e colocando em uma variável
$config = require_once("../config.php");

//Importando a classe de Conexão
require_once("../class/Conn.class.php");

//Importando a classe de Pagamentos
require_once("../class/Payment.class.php");

$accessTokenTest = $config['accesstoken_test'];


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


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        "items": [
            {
            "title": "Produto Teste",
            "description": "Dummy description",
            "picture_url": "https://www.myapp.com/myimage.jpg",
            "category_id": "car_electronics",
            "quantity": 1,
            "currency_id": "BRL",
            "unit_price": ' . $amount . '
            }
        ],
        
        "payment_methods": {
            "excluded_payment_methods": [
            {
                "id": "visa"
            }
            ],
            "excluded_payment_types": [
            {
                "id": "ticket"
            }
            ],
            "default_payment_method_id": "amex",
            "installments": 10,
            "default_installments": 5
        },
        "shipments": {
            "local_pickup": false,
            "dimensions": "32 x 25 x 16",
            "default_shipping_method": null,
            "free_methods": [
            {
                "id": null
            }
            ]
        },
        "back_urls": {
            "success": "https://google.com/success",
            "pending": "https://google.com/pending",
            "failure": "https://google.com/failure"
        },
        "notification_url": "' . $config['url_notification_api'] . '",
        "auto_return": "approved",
        "external_reference": "' . $payCreate . '",
        "metadata": null
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer APP_USR-2899009929281059-120120-bf555d0fe59391667050d2c836000ab8-2129182779'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;


        //Decodificando a resposta JSON e armazenando em uma variável 
        $obj = json_decode($response, true);

        // var_dump($obj);

        //Se o atributo ID estiver presente e for diferente de nulo
        if (isset($obj['id']) && $obj['id'] !== null) {

            if (isset($card)) {
                $preference_id = $obj['id'];
            } else {
                //Campo que guarda o link de pagamento externo / ticket
                $link_externo = $obj['init_point'];

                //Campo referente ao ID do pagamento (que será armazenado no BD)
                $external_reference = $obj['external_reference'];


                //Variável com o código HTML que envolverá os parâmetros de pagamento

                $html = "
                        <h3>{$amount} #{$external_reference}</h3>
                        <a href='{$link_externo}' target='_blank'>Link Externo</a>
                    ";

                echo $html;

                // echo $copia_e_cola;
            }

        }


    } else {
        echo 'NÃO FOI POSSÍVEL GERAR SEU LINK SEU PAGAMENTO!';
    }
}

