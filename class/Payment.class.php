<?php

//Classe referente aos Pagamentos
class Payment
{
    //Atributos (privados)
    private $pdo;
    private $user_id = null;

    public $payment_id = null;

    //Construtor

    public function __construct($user_id = null)
    {
        $this->user_id = $user_id;

        //Resgata o método de conexão da classe DB
        $this->pdo = DB::getInstance();
    }


    // C.R.U.D da Entidade Payments

    //Método para resgatar as informações do Pagamento do BD
    public function get()
    {
        //Utilizando a classe PDO
        $pdo = $this->pdo;
        $selectUser = "SELECT * FROM `payments` WHERE id = :id";
        $query = $pdo->prepare($selectUser);
        $query->bindValue(':id', $this->payment_id);

        if ($query->execute()) {

            $row = $query->fetchAll(PDO::FETCH_OBJ);

            if (count($row) > 0) {
                return $row[0];
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    //Método para inserir um novo Pagamento

    public function addPayment($valor)
    {
        $pdo = $this->pdo;
        $insertPayment = "INSERT INTO `payments` (valor, user_id) VALUES (:valor, :user_id); ";
        $query = $pdo->prepare($insertPayment);
        $query->bindValue(":valor", $valor);
        $query->bindValue(":user_id", $this->user_id);

        if ($query->execute()) {
            return $this->pdo->lastInsertId(); //Retorna o último ID registrado na tabela do BD
        } else {
            return false;
        }
    }


    //Método para modificar o STATUS do Pagamento

    public function setStatusPayment($status)
    {
        $pdo = $this->pdo;
        $updatePayment = "UPDATE `payments` SET status = :status WHERE id = :id";
        $query = $pdo->prepare($updatePayment);
        $query->bindValue(":status", $status);
        $query->bindValue(":id", $this->payment_id);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

}


?>