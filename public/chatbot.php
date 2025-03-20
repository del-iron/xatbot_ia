<?php
// Este arquivo é o backend principal do chatbot. Ele processa mensagens do usuário, chama a API de IA e retorna respostas.
header("Content-Type: text/plain");
session_start();

include(__DIR__ . "/../includes/paramentros.php"); // Caminho atualizado

// Inicializa variáveis de sessão
function initialize_session() {
    if (!isset($_SESSION['erro_count'])) {
        $_SESSION['erro_count'] = 0;
    }
    if (!isset($_SESSION["chat_started"])) {
        $_SESSION["chat_started"] = true;
        simulate_delay(1000000); // 1 segundo
        paramentros::send_response(paramentros::get('welcome_message'));
    }
}

// Obtém a mensagem do usuário
function get_user_message() {
    return isset($_POST["message"]) ? strtolower(trim($_POST["message"])) : "";
}

// Obtém o nome do usuário
function get_user_name() {
    return $_SESSION["user_name"] ?? paramentros::get('default_user_name');
}

// Função para gerar resposta usando IA
function gerar_resposta_ia($message) {
    $api_key = paramentros::get('api_key'); // Obtém a chave da API do config
    $url = paramentros::get('api_url'); // Obtém a URL da API do config
    // Prepara os dados com as colunas pergunta, resposta e contexto
    $data = [
        // Define as entradas para a API
        'inputs' => [
            // Define a pergunta como a mensagem do usuário
            'pergunta' => $message,
            // Define o contexto como o contexto da sessão
            'contexto' => $_SESSION['contexto'] ?? '',
        ],
    ];
    // Faz a requisição para a API
    $response = make_api_request($url, $data, $api_key);
    // Verifica se a resposta está presente
    if (isset($response[0]['resposta'])) {
        // Atualiza o contexto da sessão
        $_SESSION['contexto'] = $response[0]['contexto'] ?? '';
        // Retorna a resposta gerada pela IA
        return $response[0]['resposta'];
        // Retorna a resposta gerada pela IA
    } else {
        // Loga o erro da resposta da API
        error_log('API Response Error: ' . print_r($response, true));
        return null;
    }
}

// Realiza a requisição para a API
function make_api_request($url, $data, $api_key) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);

    if ($result === FALSE) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    return json_decode($result, true);
}

// Função para lidar com respostas padrão
function handle_default_response($user_name) {
    $_SESSION['erro_count']++;
    switch ($_SESSION['erro_count']) {
        case 1:
            return "$user_name, desculpe, não encontrei uma resposta para isso. Reformule sua pergunta, por favor!";
        case 2:
            return "$user_name, não consegui entender sua solicitação. Poderia reformular de outra maneira?";
        default:
            session_unset();
            session_destroy();
            return "$user_name, sinto muito, não consegui te entender. Encerrando o chat!";
    }
}

// Função para simular resposta humana
function simulate_human_response() {
    simulate_delay(rand(2000000, 4000000)); // Entre 2 e 4 segundos
}

// Função para simular atraso
function simulate_delay($microseconds) {
    usleep($microseconds);
}

// Fluxo principal
initialize_session();
// Obtém a mensagem do usuário
$message = get_user_message();
// Obtém o nome do usuário
$user_name = get_user_name();
// Gera a resposta da IA
$resposta = gerar_resposta_ia($message) ?? handle_default_response($user_name);
// Simula atraso na resposta
simulate_human_response();
// Envia a resposta ao cliente
paramentros::send_response($resposta);
?>