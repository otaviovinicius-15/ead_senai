<?php
// ============================================
// Arquivo: logout.php
// Função: Encerrar a sessão do usuário
// ============================================

session_start();
session_unset();
session_destroy();

header("Location: index.php");
exit;
