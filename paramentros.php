<?php
class paramentros {
    // Carrega as configurações do arquivo config.php
    private static $config;
    // Carrega as configurações do arquivo config.php
    public static function load_config() {
        if (!self::$config) {
            self::$config = include("config.php");
        }
    }

    // Obtém uma configuração específica
    public static function get($key) {
        self::load_config();
        return self::$config[$key] ?? null;
    }

    // Função para enviar resposta e encerrar o script
    public static function send_response($response) {
        echo $response;
        exit;
    }
}
?>