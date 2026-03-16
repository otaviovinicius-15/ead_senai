<?php
// ============================================
// Arquivo: admin/curso_delete.php
// Função: Excluir um curso cadastrado
// ============================================

session_start();

// Verifica se está logado e se é admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

// Verifica se recebeu o ID do curso e não está vazio
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Busca os dados do curso para deletar a imagem de capa (se existir)
    $sql_busca = "SELECT capa FROM cursos WHERE id = '$id'";
    $resultado = mysqli_query($conexao, $sql_busca);

    if ($curso = mysqli_fetch_assoc($resultado)) {
        // Se tem capa e o arquivo existe
        if (!empty($curso['capa']) && file_exists("../uploads/capas/" . $curso['capa'])) {
            unlink("../uploads/capas/" . $curso['capa']);
        }
    }

    // O banco de dados foi configurado com ON DELETE CASCADE, 
    // então excluir o curso vai excluir as aulas, modulos e inscrições relacionadas
    $sql_delete = "DELETE FROM cursos WHERE id = '$id'";

    if (mysqli_query($conexao, $sql_delete)) {
        header("Location: cursos.php?msg=excluido");
        exit;
    }
    else {
        echo "Erro ao excluir o curso: " . mysqli_error($conexao);
    }
}
else {
    // Redireciona de volta se acessado diretamente sem ID
    header("Location: cursos.php");
    exit;
}
?>
