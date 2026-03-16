<?php
// ============================================
// Arquivo: logado.php
// Função: Restringir acesso de páginas que exigem login
// ============================================

// Verificar se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
