<?php
// Menu superior do Aluno
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-senai-blue shadow-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
        <a href="cursos.php" class="flex items-center gap-2 text-white font-extrabold text-lg">
            🎓 <span class="hidden sm:inline">EAD SENAI</span>
        </a>
        
        <div class="flex items-center gap-6">
            <a href="cursos.php" class="<?php echo($pagina_atual == 'cursos.php') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white'; ?> text-sm transition transition">Catálogo de Cursos</a>
            <a href="meus_cursos.php" class="<?php echo($pagina_atual == 'meus_cursos.php') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white'; ?> text-sm transition transition">Meus Cursos</a>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right hidden md:block">
                <p class="text-white text-xs font-semibold leading-tight"><?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?></p>
                <p class="text-blue-300 text-[10px] leading-tight mt-0.5">Aluno</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold text-sm">
                <?php echo strtoupper(substr($_SESSION["usuario_nome"], 0, 1)); ?>
            </div>
            <a href="logout.php" class="text-blue-200 hover:text-red-400 text-sm transition" title="Sair do sistema">🚪</a>
        </div>
    </div>
</nav>
