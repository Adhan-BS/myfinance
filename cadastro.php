<?php
require_once 'conexao.php';

// 1. Dados de entrada enviados pelo formulário
$nome_completo = $_POST['nome_completo'] ?? '';
$email         = $_POST['email'] ?? '';
$cpf           = $_POST['cpf'] ?? '';
$telefone      = $_POST['telefone'] ?? '';
$senha_pura    = $_POST['senha'] ?? '';
$papel         = "usuario";

if (
    empty($nome_completo) ||
    empty($email) ||
    empty($cpf) ||
    empty($telefone) ||
    empty($senha_pura)
) {
    die("Preencha todos os campos obrigatórios.");
}

// 2. Hash gerado APÓS a variável $senha_pura existir
$senha_criptografada = password_hash($senha_pura, PASSWORD_DEFAULT);

// 3. Execução protegida por try/catch
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
    echo "Usuário cadastrado com sucesso no MyFinance!";

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Erro de validação: Este e-mail ou CPF já está cadastrado.";
    } else {
        error_log("Erro no MyFinance: " . $e->getMessage());
        echo "Ocorreu um erro interno. Tente novamente mais tarde.";
    }
}
?>