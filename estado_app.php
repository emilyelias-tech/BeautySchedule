<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Dados Pessoais
if (!isset($_SESSION['dados_pessoais'])) {
    $_SESSION['dados_pessoais'] = [
        'nome' => 'Emily Elias',
        'email' => 'emilyelias@gmail.com',
        'data_nasc' => '15/05/1998',
        'telefone' => '(11) 98765-4321',
        'cpf' => ''
    ];
}

// 2. Senha da Conta
if (!isset($_SESSION['senha_usuario'])) {
    $_SESSION['senha_usuario'] = '123456';
}

// 3. Configurações e Acessibilidade
if (!isset($_SESSION['configuracoes'])) {
    $_SESSION['configuracoes'] = [
        'tema' => 'claro',
        'texto_maior' => false,
        'alto_contraste' => false,
        'reduzir_animacoes' => false,
        'notif_agendamento' => true,
        'notif_email' => true,
        'notif_pedidos' => true,
        'notif_novidades' => false,
        'seg_biometria' => false,
        'seg_perfil_visivel' => true,
        'idioma' => 'pt-BR',
        'fuso' => 'America/Sao_Paulo'
    ];
}

// 4. Dispositivos Conectados
if (!isset($_SESSION['dispositivos'])) {
    $_SESSION['dispositivos'] = [
        ['nome' => 'Windows • Chrome (Este dispositivo)', 'local' => 'São Caetano do Sul, SP', 'atual' => true],
        ['nome' => 'iPhone 15 • Safari', 'local' => 'São Paulo, SP', 'atual' => false]
    ];
}

// 5. Pontos e Histórico do Beauty Club
if (!isset($_SESSION['pontos'])) {
    $_SESSION['pontos'] = 680;
}

if (!isset($_SESSION['historico_pontos'])) {
    $_SESSION['historico_pontos'] = [
        ['data' => '20/11/2026', 'descricao' => 'Limpeza de Pele Profunda', 'pontos' => '+150', 'tipo' => 'ganho'],
        ['data' => '05/11/2026', 'descricao' => 'Hidratação Facial + Máscara de Ouro', 'pontos' => '+220', 'tipo' => 'ganho'],
        ['data' => '18/10/2026', 'descricao' => 'Resgate: Desconto de R$ 20,00', 'pontos' => '-300', 'tipo' => 'resgate'],
        ['data' => '02/10/2026', 'descricao' => 'Design de Sobrancelhas & Spa Labial', 'pontos' => '+110', 'tipo' => 'ganho'],
        ['data' => '15/09/2026', 'descricao' => 'Bônus de Boas-vindas Beauty Club', 'pontos' => '+500', 'tipo' => 'ganho']
    ];
}

// Variáveis globais prontas para todas as telas
$nome_cartao = $_SESSION['dados_pessoais']['nome'];
$email_cartao = $_SESSION['dados_pessoais']['email'];

$partes_nome = array_values(array_filter(explode(' ', $nome_cartao)));
if (count($partes_nome) >= 2) {
    $iniciais = strtoupper(substr($partes_nome[0], 0, 1) . substr(end($partes_nome), 0, 1));
} elseif (count($partes_nome) === 1) {
    $iniciais = strtoupper(substr($partes_nome[0], 0, 2));
} else {
    $iniciais = 'BS';
}

$cfg = $_SESSION['configuracoes'];
$classes_body = [];
if ($cfg['tema'] === 'escuro') $classes_body[] = 'tema-escuro';
if ($cfg['texto_maior']) $classes_body[] = 'texto-maior';
if ($cfg['alto_contraste']) $classes_body[] = 'alto-contraste';
if ($cfg['reduzir_animacoes']) $classes_body[] = 'reduzir-animacoes';
$attr_body_class = implode(' ', $classes_body);