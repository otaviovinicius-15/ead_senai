<?php
// Menu de navegação lateral do Administrador
?>
<aside class="w-56 bg-gray-900 min-h-screen flex flex-col flex-shrink-0">
    <!-- Logo -->
    <div class="px-4 py-5 border-b border-gray-700">
        <p class="text-white font-extrabold text-base">🎓 EAD SENAI</p>
        <p class="text-gray-500 text-xs mt-0.5">Painel Administrativo</p>
    </div>
    <!-- Info admin -->
    <div class="px-4 py-3 border-b border-gray-700">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-xs font-bold">
                <?php echo strtoupper(substr($_SESSION["usuario_nome"], 0, 1)); ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-white text-xs font-semibold truncate"><?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?></p>
                <p class="text-gray-500 text-xs truncate"><?php echo htmlspecialchars($_SESSION["usuario_email"]); ?></p>
            </div>
        </div>
    </div>
    <!-- Menu -->
    <nav class="flex-1 p-3 space-y-1">
        <?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
        <a href="index.php" class="nav-link <?php echo($pagina_atual == 'index.php') ? 'active' : ''; ?>">📊 <span>Dashboard</span></a>
        <a href="cursos.php" class="nav-link <?php echo(strpos($pagina_atual, 'curso') !== false && strpos($pagina_atual, 'cursos.php') === false && strpos($pagina_atual, 'meus_') === false) || $pagina_atual == 'cursos.php' ? 'active' : ''; ?>">📚 <span>Cursos</span></a>
        <a href="modulos.php" class="nav-link <?php echo(strpos($pagina_atual, 'modulo') !== false) ? 'active' : ''; ?>">📦 <span>Módulos</span></a>
        <a href="aulas.php" class="nav-link <?php echo(strpos($pagina_atual, 'aula') !== false && strpos($pagina_atual, 'aulas.php') !== false) || $pagina_atual == 'aulas.php' ? 'active' : ''; ?>">🎬 <span>Aulas</span></a>
        <div class="pt-2 border-t border-gray-700 mt-2">
            <a href="../meus_cursos.php" class="nav-link">👁 <span>Ver site</span></a>
            <a href="../logout.php" class="nav-link text-red-400 hover:text-red-300">🚪 <span>Sair</span></a>
        </div>
    </nav>
</aside>
