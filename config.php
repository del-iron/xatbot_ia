<?php
// filepath: c:\xampp\htdocs\xatbot_ia\includes\config.php

return [
    'api_key' => getenv('HUGGINGFACE_API_KEY'),
    'api_url' => 'https://api-inference.huggingface.co/models/gpt2',
    'welcome_message' => "Olá, eu sou Toinha! Como posso te ajudar hoje?",
    'default_user_name' => "Usuário",
];