<?php
class Produto{
    private $id_produto;
    private $nome;
    private $descricao;
    private $valor
    private $pdo;


    public function conecta(){
        try {
            $dns = "mysql:dbname=loja_db;host=localhost";
            $dbUser = "root";
            $dbPass = "";
            $this->pdo = new PDO($dns, $dbUser, $dbPass);
            return true;
        } catch (/Throwable $th){
            return false;
        }

    }
    public function enviarProduto
 


}