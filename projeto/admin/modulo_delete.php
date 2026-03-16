<?php
// ============================================
// Arquivo: admin/modulo_delete.php
// Função: Excluir um módulo cadastrado
// ============================================

session_start();

// Verifica se está logado e se é admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

// Verifica se recebeu o ID do módulo e não está vazio
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : '';

    // O banco de dados foi configurado com ON DELETE CASCADE, 
    // então excluir o módulo vai excluir as aulas relacionadas
    $sql_delete = "DELETE FROM modulos WHERE id = '$id'";

    if (mysqli_query($conexao, $sql_delete)) {
        header("Location: modulos.php?curso_id=$curso_id&msg=excluido");
        exit;
    }
    else {
        echo "Erro ao excluir o módulo: " . mysqli_error($conexao);
    }
}
else {
    // Redireciona de volta se acessado diretamente sem ID
    header("Location: cursos.php");
    exit;
}
?>
