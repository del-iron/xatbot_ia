<?php
// Esta classe fornece métodos utilitários para carregar configurações e enviar respostas ao cliente.
class paramentros {
    // Carrega as configurações do arquivo config.php
    private static $config;

    public static function load_config() {
        if (!self::$config) {
            self::$config = include(__DIR__ . "/config.php"); // Caminho atualizado
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
