<?php
header("Content-Type: text/plain");
session_start();  // Inicia a sessão

include ("paramentros.php");

// Inicializa erro_count se ainda não estiver definido
if (!isset($_SESSION['erro_count'])) {
    $_SESSION['erro_count'] = 0;
}

$message = isset($_POST["message"]) ? strtolower(trim($_POST["message"])) : "";

// Verifica se o chat acabou de ser aberto
if (!isset($_SESSION["chat_started"])) {
    $_SESSION["chat_started"] = true;
    usleep(1000000); // 1 segundo
    paramentros::send_response(paramentros::WELCOME_MESSAGE);
}

$user_name = $_SESSION["user_name"] ?? paramentros::DEFAULT_USER_NAME;

// Função para gerar resposta usando IA
function gerar_resposta_ia($message) {
    $api_key = 'curl -X GET \
     ""'; // chave de API da Hugging Face
    $url = 'https://api-inference.huggingface.co/models/gpt2'; // URL da API da Hugging Face
    
    $data = [
        'inputs' => $message,
    ];
    
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
    
    $response = json_decode($result, true);
    error_log('API Response: ' . print_r($response, true));
    
    return $response[0]['generated_text'] ?? null;
}

$resposta = gerar_resposta_ia($message);

// Se nenhuma resposta foi gerada, usar resposta padrão
if ($resposta === null) {
    $_SESSION['erro_count']++;

    switch ($_SESSION['erro_count']) {
        case 1:
            $resposta = "$user_name, desculpe, não encontrei uma resposta para isso. Reformule sua pergunta, por favor!";
            break;
        case 2:
            $resposta = "$user_name, não consegui entender sua solicitação. Poderia reformular de outra maneira?";
            break;
        default:
            $resposta = "$user_name, sinto muito, não consegui te entender. Encerrando o chat!";
            session_unset();
            session_destroy();
            break;
    }
}

// Simula resposta humana
usleep(rand(2000000, 4000000)); // Entre 2 e 4 segundos

paramentros::send_response($resposta);
?>