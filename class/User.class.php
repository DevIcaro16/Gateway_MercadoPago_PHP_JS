<?php

//Classe referente ao Usuário
class User
{
    //Atributos da class

    private $pdo;

    private $user_id = null;

    //Construtor
    public function __construct($user_id = null) //ID do usuário
    {
        $this->user_id = $user_id;

        //Resgata o método de conexão da class DB e verifica se já há alguma istância
        $this->pdo = DB::getInstance();
    }

    //Função/Método que adiciona ao saldo do usuário no BD

    public function addBalance($valor)
    {
        $pdo = $this->pdo;
        $updateBalance = "UPDATE `user` SET balance = balance + :balance WHERE id = :id";
        $query = $pdo->prepare($updateBalance);
        $query->bindValue(":balance", $valor);
        $query->bindValue(":id", $this->user_id);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Função / Método que retorna os dados do usuário direto do BD
    public function get()
    {

        //Utilizando a classe PDO
        $pdo = $this->pdo;
        $selectUser = "SELECT id, username, balance FROM `user` WHERE id = :id";
        $query = $pdo->prepare($selectUser);
        $query->bindValue(':id', $this->user_id);

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
}

?>