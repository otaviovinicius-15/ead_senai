<?php
// ============================================
// Arquivo: admin/aula_delete.php
// Função: Excluir uma aula cadastrada
// ============================================

session_start();

// Verifica se está logado e se é admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

// Verifica se recebeu o ID da aula
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $modulo_id = isset($_GET['modulo_id']) ? $_GET['modulo_id'] : '';
    $curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : '';

    $sql_delete = "DELETE FROM aulas WHERE id = '$id'";

    if (mysqli_query($conexao, $sql_delete)) {
        header("Location: aulas.php?modulo_id=$modulo_id&curso_id=$curso_id&msg=excluido");
        exit;
    }
    else {
        echo "Erro ao excluir a aula: " . mysqli_error($conexao);
    }
}
else {
    // Redireciona de volta se acessado diretamente
    header("Location: cursos.php");
    exit;
}
?>
