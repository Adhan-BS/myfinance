<?php
header('Content-Type: application/json; charset=utf-8');

// Função única para todas as respostas da API
function responder(int $status, bool $sucesso, string $mensagem): void
{
    http_response_code($status);
    echo json_encode([
        'sucesso'  => $sucesso,
        'mensagem' => $mensagem
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once 'conexao.php';

// 1. Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, false, 'Método não permitido.');
}

// 2. Lê o corpo da requisição como JSON
$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    responder(400, false, 'Corpo da requisição inválido. Envie um JSON.');
}

// 3. Garante que os campos, se enviados, são texto
foreach (['nome_completo', 'email', 'cpf', 'telefone', 'senha'] as $campo) {
    if (isset($dados[$campo]) && !is_string($dados[$campo])) {
        responder(400, false, "O campo '$campo' deve ser um texto.");
    }
}

// 4. Limpeza e captura dos dados
$nome_completo = trim($dados['nome_completo'] ?? '');
$email         = trim($dados['email'] ?? '');
$cpf           = preg_replace('/\D/', '', $dados['cpf'] ?? '');
$telefone      = preg_replace('/\D/', '', $dados['telefone'] ?? '');
$senha_pura    = $dados['senha'] ?? '';
$papel         = 'usuario'; // sempre fixo no servidor

// 5. Validações
if (
    $nome_completo === '' ||
    $email === '' ||
    $cpf === '' ||
    $telefone === '' ||
    $senha_pura === ''
) {
    responder(400, false, 'Preencha todos os campos obrigatórios.');
}

if (mb_strlen($nome_completo) > 100) {
    responder(400, false, 'O nome deve ter no máximo 100 caracteres.');
}

if (mb_strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(400, false, 'E-mail inválido.');
}

if (strlen($cpf) !== 11) {
    responder(400, false, 'O CPF deve conter exatamente 11 dígitos.');
}

if (strlen($telefone) < 10 || strlen($telefone) > 11) {
    responder(400, false, 'O telefone deve conter 10 ou 11 dígitos.');
}

if (strlen($senha_pura) < 8 || strlen($senha_pura) > 72) {
    responder(400, false, 'A senha deve ter entre 8 e 72 caracteres.');
}

// 6. Hash da senha
$senha_criptografada = password_hash($senha_pura, PASSWORD_DEFAULT);

// 7. Inserção protegida no banco
try {
    $sql = "INSERT INTO usuarios (nome_completo, email, cpf, telefone, senha_hash, papel)
            VALUES (:nome, :email, :cpf, :telefone, :senha, :papel)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome_completo);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':cpf', $cpf);
    $stmt->bindValue(':telefone', $telefone);
    $stmt->bindValue(':senha', $senha_criptografada);
    $stmt->bindValue(':papel', $papel);
    $stmt->execute();

    responder(201, true, 'Usuário cadastrado com sucesso no MyFinance!');

} catch (PDOException $e) {
    if (($e->errorInfo[1] ?? null) == 1062) {
        responder(409, false, 'Este e-mail ou CPF já está cadastrado.');
    }

    error_log('Erro no MyFinance (cadastro): ' . $e->getMessage());
    responder(500, false, 'Ocorreu um erro interno. Tente novamente mais tarde.');
}