<?php

namespace App\Funcs;

use Exception;
use PDO;
use PDOException;
use App\Funcs\Config;

date_default_timezone_set('America/Sao_Paulo');

class CommonFuncs
{

    public $db;
    public $hostname = '';
    public $username = '';
    public $password = '';
    public function __construct()
    {

        $this->db = getenv('DB_DATABASE');
        $this->hostname = getenv('DB_HOST');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
    }

    /**
     * Instancia uma conexão PDO com o banco informado!
     */
    public function conn($db_name = false, $host = $this->hostname)
    {

        $username = $this->username;

        $password = $this->password;

        if ($db_name == false) {
            return 'Erro: Banco não informado!';
        }

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        } catch (PDOException $e) {
            return "Erro com bando de dados: " . $e->getMessage() . " HOST: " . $host . " DB_NAME: " . $db_name . " username: " . $username . " password: " . $password;
        } catch (Exception $e) {
            return "Erro genérico " . $e->getMessage();
        }

        return $pdo;
    }

    /**
     * Excecuta uma query passada por parametro
     */
    public function db_execute(PDO $conn, $query = false)
    {

        if (empty($query))
            return "Erro: query vazia!";
        return $conn->query($query);
    }
    /**
     * Gera um numero aleatorio
     * @return
     */
    public function generation_num()
    {
        $date = date('Ymd');
        $time = time();
        $sol = null;

        $sol = $date * $time;
        $sol = substr($sol, -6);

        return $sol;
    }



    /**
     * Criando meu proprio JWT
     */

    /**
     * Função que gera um token aleatório para o usuário
     *
     * @param  string $id
     * @return string
     */
    public function generation_token(string $id, $new = false): string
    {
        /**
         * @var string $hash vem do .ENV
         * @var int $random numero gerado entre 0 e o comprimento da string do hash
         * @var json $payload estura dos daddos e enconde para gerar o token
         * @var array $res json de retorno
         */

        $db = getenv('DB_BASE');
        $hash = getenv('HASH');
        $random = rand(0, strlen($hash) - 1);
        $date = date('Y-m-d H:i:s');
        $msg = true;

        $payload = array(
            'id' => $id,
            'data_login' => date("Y-m-d H:i:s"),
            'hash' => $hash,
            'random' => $random,
            'letra' => substr($hash, $random, 1)
        );

        // $payload = array(
        //     'id' => 1234,
        //     'data_login' => 'aa',
        //     'hash' => 'a',
        //     'random' => 'a',
        //     'letra' => 'a'
        // );

        $payload = base64_encode(json_encode($payload));
        $token = password_hash($payload, PASSWORD_DEFAULT);

        $expiration_time = date('Y-m-d H:i:s', strtotime("+60 minute"));

        $conn = $this->conn($db);
        if ($new == false) {
            $query = "INSERT INTO auditoria_login (token, payload, dt_login, expiration_time, id)
                    VALUES ('$token', '$payload', '$date', '$expiration_time', '$id')";
        } else {
            $query = "UPDATE auditoria_login
                        SET token = '$token',
                        payload = '$payload',
                        expiration_time = '$expiration_time'
                    WHERE id = '$id'";
        }

        try {
            $result = $this->db_execute($conn, $query);
            $result = $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $pdo_error) {
            Config::write_log(['POST' => $_POST, 'ERRO' => $pdo_error], 'LogPostErrorPDO_generation_token');
            $msg =  json_encode(array('auth' => false, 'msg' => 'Error de consulta de base de datos'));
        } catch (Exception $e) {
            Config::write_log(['POST' => $_POST, 'ERRO' => $e], 'LogPostErrorGeneric_generation_token');
            $msg = json_encode(array('auth' => false, 'msg' => $e->getMessage()));
        }

        return json_encode(['auth' => $msg, 'token' => $token]);
    }


    /**
     * Função que valida o token do usuário
     * @param  string $id
     * @return string
     */
    public function verify_token(string $token, string $id): string
    {
        if ($token === '' && !empty($id)) {
            return $this->generation_token($id);
        }

        $db = $_ENV['DB_BASE'];
        $ret = array('auth' => false, 'msg' => 'Token invalido');
        $conn = $this->conn($db);

        $query = "SET sql_mode='NO_BACKSLASH_ESCAPES';SELECT payload, dt_login, expiration_time FROM auditoria_login where token = '$token' AND id ='$id'";

        try {
            $result = $this->db_execute($conn, $query);
            $result_set = $result->rowCount();
            $result->nextRowset();
            $result = $result->fetch(PDO::FETCH_ASSOC);
            // Config::write_log(['query' => $query, 'return' => $result, 'resultu_ser' => $result_set], 'aaa');
        } catch (PDOException $pdo_error) {
            Config::write_log(['POST' => '$_POST', 'ERRO' => $pdo_error->getMessage(), 'query' => $query], 'LogPostErrorPDO_verify_token');
            $ret =  array('auth' => false, 'msg' => 'Error de consulta de base de datos');
        } catch (Exception $e) {
            Config::write_log(['POST' => $_POST, 'ERRO' => $e], 'LogPostErrorGeneric_verify_token');
            $ret = array('auth' => false, 'msg' => $e->getMessage());
        }

        if (password_verify($result['payload'] ?? '', $token)) {

            $exp = $result['expiration_time'];

            if (date('Y-m-d H:i:s') <= $exp) {
                //dentro da validade
                $ret = array('auth' => true, 'msg' => 'Token valido');
            } else {
                ['id' => $id] = json_decode(base64_decode($result['payload']), true);
                $new_token = json_decode($this->refresh_token($id), true);
                $ret = array('auth' => true, 'token' => $new_token['token']);
            }
        }

        return json_encode($ret);
    }

    /**
     * Função que valida o token do usuário
     * @param  string $id
     * @return string
     */
    public function refresh_token(string $id): string
    {
        return $this->generation_token($id, true);
    }

    /**
     * Funcao remove caracteres em branco ou especificados de uma string.
     *
     * @param string $string A string que sera modificada.
     * @param string $tipo_trim (opcional) O tipo de trim: 'r' para trim à direita, 'l' para trim à esquerda ou uma string
     * vazia para trim em ambos os lados. O padrão é trim em ambos os lados.
     * @param string $caractere (opcional) O caractere que será removido. O padrão é espaço em branco.
     *
     * @return string A string modificada.
     */
    function str_trim($string, $tipo_trim = '', $caractere = '')
    {
        if ($tipo_trim == 'r') {
            return rtrim($string, $caractere);
        } elseif ($tipo_trim == 'l') {
            return ltrim($string, $caractere);
        } else {
            return trim($string, $caractere);
        }
    }
}
