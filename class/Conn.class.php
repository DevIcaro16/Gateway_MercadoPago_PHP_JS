<?php

//Classe de Conexão com a Base de Dados
class DB
{
    //Atributos da class (Credenciais de Conexão):
    private $host;
    private $user;
    private $password;
    private $database;
    private $port;
    public $pdo;

    //Conceitos de Singleton ->  forma de garantir que uma classe tenha apenas uma instância 
    //(um único objeto) ao longo do programa. Evita disperdícios de memória e previne de ocorrer
    // novas conexões desnecessárias ao decorrer do código.

    //Atributo estático -> todas as partes do programa acessam a mesma variável
    private static $instance = null;

    //Construtor Privado
    private function __construct()
    {

        //Minhas Credenciais de Conexão Local

        $this->host = "127.0.0.1";
        $this->port = "3306";
        $this->user = "root";
        $this->password = "Irpgamerbr_17";
        $this->database = "pagamentos";

        // Class PDO

        $this->pdo = new PDO(
            "mysql:host=$this->host:$this->port;dbname=$this->database",
            $this->user,
            $this->password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8MB4")
        );
    }

    //Método estático que retorna a conexão com o BD utilizando o PDO
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;

    }
}

?>