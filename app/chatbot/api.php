
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function responder($mensagem, $codigo = 200) {
    http_response_code($codigo);

    echo json_encode([
        'resposta' => $mensagem
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    responder('Você precisa entrar na sua conta.', 401);
}

// Verifica o tipo de usuário
$tipoUsuario = $_SESSION['tipo_usuario'] ?? '';

$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    responder('Dados inválidos.', 400);
}

$tipoChatbot = $dados['tipo'] ?? '';
$mensagem = trim($dados['mensagem'] ?? '');

if ($tipoChatbot !== $tipoUsuario) {
    responder('Você não tem permissão para acessar este assistente.', 403);
}

if (!in_array($tipoChatbot, ['cliente', 'funcionario'], true)) {
    responder('Tipo de assistente inválido.', 400);
}

if ($mensagem === '' || mb_strlen($mensagem) > 1000) {
    responder('Digite uma mensagem válida.', 400);
}

// Obtém a chave da API
$chave = getenv('OPENAI_API_KEY');

if (!$chave) {
    responder('A chave da inteligência artificial não foi configurada.', 500);
}

// Instruções específicas para cada chatbot
if ($tipoChatbot === 'cliente') {

    $instrucoes = "
    Você é a assistente virtual do BeautySchedule.

    Seu público são os clientes do estabelecimento.

    Você deve:
    - Responder dúvidas sobre o funcionamento do sistema.
    - Explicar como realizar agendamentos.
    - Ajudar com dúvidas sobre serviços.
    - Ser educada, clara e objetiva.
    - Responder sempre em português.

    Não invente preços, horários ou informações.
    Não revele informações de outros clientes.
    Não execute operações no banco de dados.
    ";

} else {

    $instrucoes = "
    Você é a assistente administrativa do BeautySchedule.

    Seu público são os funcionários do estabelecimento.

    Você deve:
    - Ajudar com dúvidas sobre o sistema.
    - Explicar procedimentos administrativos.
    - Auxiliar na organização dos atendimentos.
    - Responder sempre em português.
    - Ser educada, clara e objetiva.

    Não invente dados de clientes ou agendamentos.
    Não revele informações sem autorização.
    Não execute operações no banco de dados.
    ";
}

// Prepara a requisição
$dadosAPI = [
    'model' => 'gpt-6.2-astra',
    'instructions' => $instrucoes,
    'input' => $mensagem,
    'max_output_tokens' => 500
];

$curl = curl_init('https://api.openai.com/v1/responses');

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $chave
    ],
    CURLOPT_POSTFIELDS => json_encode($dadosAPI),
    CURLOPT_TIMEOUT => 60
]);

$resultado = curl_exec($curl);

$erro = curl_error($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

if ($resultado === false) {
    responder('Não foi possível conectar à inteligência artificial.', 500);
}

$respostaAPI = json_decode($resultado, true);

if ($status < 200 || $status >= 300) {
    responder('Ocorreu um erro ao consultar a inteligência artificial.', 502);
}

// Localiza o texto da resposta
$respostaTexto = '';

foreach ($respostaAPI['output'] ?? [] as $item) {

    if (($item['type'] ?? '') === 'message') {

        foreach ($item['content'] ?? [] as $conteudo) {

            if (($conteudo['type'] ?? '') === 'output_text') {
                $respostaTexto .= $conteudo['text'];
            }

        }
    }
}

if ($respostaTexto === '') {
    responder('Não consegui gerar uma resposta. Tente novamente.', 502);
}

responder($respostaTexto);

?>
