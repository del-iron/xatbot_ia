<?php
header("Content-Type: text/plain");
session_start();  // Inicia a sessão

include("paramentros.php");

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
    $api_key = 'SUA_CHAVE_DE_API_AQUI'; // Substitua pela sua chave de API da Hugging Face
    $url = 'https://api-inference.huggingface.co/models/gpt2'; // URL da API da Hugging Face

    // Prepara os dados com as colunas pergunta, resposta e contexto
    $data = [
        'inputs' => [
            'pergunta' => $message,
            'contexto' => $_SESSION['contexto'] ?? '', // Usa o contexto armazenado na sessão, se disponível
        ],
    ];

    // Inicializa o cURL para realizar uma requisição HTTP para a API
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Define o tipo de conteúdo como JSON
        'Authorization: Bearer ' . $api_key, // Adiciona o token de autorização no cabeçalho
    ]);
    curl_setopt($ch, CURLOPT_POST, true); // Define o método HTTP como POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Envia os dados no corpo da requisição

    // Executa a requisição
    $result = curl_exec($ch);

    // Verifica se houve erro na requisição
    if ($result === FALSE) {
        error_log('cURL Error: ' . curl_error($ch)); // Loga o erro do cURL
        curl_close($ch); // Fecha o recurso cURL
        return null;
    }

    curl_close($ch); // Fecha o recurso cURL

    // Decodifica a resposta JSON
    $response = json_decode($result, true);

    // Verifica se a resposta contém o texto gerado
    if (isset($response[0]['resposta'])) {
        // Armazena o contexto retornado na sessão, se disponível
        if (isset($response[0]['contexto'])) {
            $_SESSION['contexto'] = $response[0]['contexto'];
        }
        return $response[0]['resposta'];
    } else {
        error_log('API Response Error: ' . print_r($response, true)); // Loga erro na resposta da API
        return null;
    }
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