<?php

//Inclusões das classes

require_once("class/Conn.class.php");
require_once("class/User.class.php");

$user = new User(1);
$dados_user = $user->get();

// var_dump($dados_user);
// die();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    <div class="main-container">
        <div class="div_balance">
            <h2>Username: <?= $dados_user->username; ?></h2>
            <h2>Saldo: R$<?= $dados_user->balance; ?></h2>

            <br>

            <input type="number" placeholder="0.00" name="valor" id="valor">

            <!-- Utilizando a API MP -->

            <div class="methods-container">
                <div class="option-container">
                    <div class="method-container-title">
                        <h3>API</h3>
                    </div>
                    <p>
                        <a href="api/pix.php">Adicionar saldo por PIX</a>
                        <a href="api/preference.php">Adicionar saldo por Link Externo</a>
                        <a href="api/card.php">Adicionar saldo por Cartão</a>
                    </p>
                </div>

                <!-- Utilizando o SDK MP -->

                <div class="option-container">
                    <div class="method-container-title">
                        <h3>SDK</h3>
                    </div>
                    <p>
                        <a href="sdk/pix.php">Adicionar saldo por PIX</a>
                        <a href="sdk/preference.php">Adicionar saldo por Link Externo</a>
                        <a href="sdk/card.php">Adicionar saldo por Cartão</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-compat/3.0.0-alpha1/jquery.min.js"
        integrity="sha512-4GsgvzFFry8SXj8c/VcCjjEZ+du9RZp/627AEQRVLatx6d60AUnUYXg0lGn538p44cgRs5E2GXq+8IOetJ+6ow=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/script.js"></script>
</body>

</html>