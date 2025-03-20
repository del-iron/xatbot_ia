<?php
class paramentros {
    // Definições de constantes
    const WELCOME_MESSAGE = "Olá, eu sou Toinha! Como posso te ajudar hoje?";
    const DEFAULT_USER_NAME = "Usuário";

    // Função para enviar resposta e encerrar o script
    public static function send_response($response) {
        echo $response;
        exit;
    }
}
?>