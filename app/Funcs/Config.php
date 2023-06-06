<?php

namespace App\Funcs;

class Config
{

    /**
     * Função que exibe um array de forma mais amigável
     * @param array|string $array
     */
    public static function print_rr($array): void
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
        echo "<hr/>";
    }

    /**
     * Função PRIMORDIAL de debug:
     * Cria um arquivo de log com o conteúdo da variável sendo passada em tempo de excecução
     * @param array|string $variavel
     */
    public static function write_log($variavel, ?string $nome): void
    {

        $id_log = !empty($nome) ? $nome : 'log_' .  __FILE__;

        $nome = $id_log . '_' . date("Y-m-d") . '.txt';

        $res = print_r($variavel, true);

        $res .= PHP_EOL . '----------------------------------------------------------------------------' . PHP_EOL;

        $log = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        date_default_timezone_set('America/Sao_Paulo');

        foreach ($log as $key => $value) {
            $res .= "BACKTRACE: ---------- : " . date("d/m/Y H:i:s",  time()) . PHP_EOL;
            $res .= print_r($value, true);
            $res .= PHP_EOL . '###########################################################################' . PHP_EOL . PHP_EOL . PHP_EOL;
        }

        // $file = fopen(getenv('COMMON_URL') . $nome, "a+");
        $file = fopen(dirname(__FILE__) . '/../logs/' . $nome, "a+");

        $escreve = fwrite($file, $res);

        fclose($file);
    }
}
