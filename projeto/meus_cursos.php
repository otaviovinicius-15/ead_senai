<?php
// ============================================
// Arquivo: meus_cursos.php
// Função: Exibir os cursos onde o aluno está inscrito
// ============================================

session_start();
require_once "logado.php";
require_once "conexao.php";

$usuario_id = $_SESSION["usuario_id"];
$nome_aluno = explode(" ", $_SESSION["usuario_nome"])[0];

$sql_inscritos = "
    SELECT 
        c.*,
        i.criado_em as data_inscricao,
        (SELECT COUNT(*) FROM modulos m WHERE m.curso_id = c.id) as total_modulos,
        (SELECT COUNT(*) FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = c.id) as total_aulas
    FROM cursos c
    INNER JOIN inscricoes i ON c.id = i.curso_id
    WHERE i.usuario_id = $usuario_id AND c.ativo = 1
    ORDER BY i.criado_em DESC
";
$resultado = mysqli_query($conexao, $sql_inscritos);
$total_inscritos = mysqli_num_rows($resultado);

$aulas_concluidas = 0;
$progresso_geral = 0;

require_once "includes/header.php";
?>
<title>Meus Cursos — EAD SENAI</title>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php require_once "includes/menu_aluno.php"; ?>

    <div class="bg-white border-b border-gray-200 px-6 py-5">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">Meus Cursos</h1>
                <p class="text-sm text-gray-500 mt-1">Bem-vindo de volta, <strong><?php echo htmlspecialchars($nome_aluno); ?></strong>! Continue de onde parou.</p>
            </div>
            <a href="cursos.php" class="border-2 border-senai-blue text-senai-blue text-sm font-semibold px-4 py-2 rounded-lg hover:bg-blue-50 transition">+ Explorar mais cursos</a>
        </div>
    </div>

    <main class="max-w-6xl mx-auto px-6 py-8 flex-1 w-full">

        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm"><p class="text-2xl font-extrabold text-senai-blue"><?php echo $total_inscritos; ?></p><p class="text-xs text-gray-500 mt-1">Cursos inscritos</p></div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm"><p class="text-2xl font-extrabold text-senai-green">0</p><p class="text-xs text-gray-500 mt-1">Aulas concluídas</p></div>
            <div class="hidden lg:block bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm"><p class="text-2xl font-extrabold text-senai-orange">0%</p><p class="text-xs text-gray-500 mt-1">Progresso geral</p></div>
        </div>

        <h2 class="font-bold text-gray-700 mb-4">Cursos em Andamento</h2>
        
        <div class="space-y-4">
            <?php if ($total_inscritos > 0): ?>
                <?php while ($curso = mysqli_fetch_assoc($resultado)): ?>
                
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100 flex flex-col md:flex-row gap-5 p-5 items-center">
                    
                    <?php if (!empty($curso['capa'])): ?>
                        <div class="w-full md:w-32 h-32 md:h-24 rounded-lg overflow-hidden flex-shrink-0 relative"><div class="absolute inset-0 bg-black/10 z-10"></div><img src="uploads/capas/<?php echo htmlspecialchars($curso['capa']); ?>" class="w-full h-full object-cover"></div>
                    <?php else: ?>
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-full md:w-24 h-24 rounded-lg flex items-center justify-center flex-shrink-0"><span class="text-4xl text-white font-bold"><?php echo strtoupper(substr($curso['titulo'], 0, 1)); ?></span></div>
                    <?php endif; ?>
                    
                    <div class="flex-1 min-w-0 w-full">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                            <div>
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">Curso Livre</span>
                                <h3 class="font-bold text-gray-800 text-base mt-1"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                <p class="text-xs text-gray-400 mt-0.5">Inscrito em <?php echo date('d/m/Y', strtotime($curso['data_inscricao'])); ?></p>
                            </div>
                            <a href="curso.php?id=<?php echo $curso['id']; ?>" class="w-full md:w-auto text-center bg-senai-blue text-white text-xs font-bold px-5 py-2.5 rounded-lg hover:bg-senai-blue-dark transition flex-shrink-0 mt-3 md:mt-0">Continuar →</a>
                        </div>
                        
                        <div class="mt-4 md:mt-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1.5"><span class="truncate pr-2">Total de conteúdos</span><span class="text-gray-400 whitespace-nowrap"><?php echo $curso['total_modulos']; ?> módulos e <?php echo $curso['total_aulas']; ?> aulas</span></div>
                            <div class="bg-gray-200 rounded-full h-2.5"><div class="bg-senai-green h-2.5 rounded-full transition-all" style="width:0%"></div></div>
                            <p class="text-xs text-gray-400 mt-1">Inicie o curso para acompanhar seu avanço.</p>
                        </div>
                        
                    </div>
                </div>
                
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-xl border border-gray-100 shadow-sm"><span class="text-5xl mb-3 block">🎒</span><h3 class="text-lg font-bold text-gray-800">Você ainda não tem cursos</h3><p class="text-sm text-gray-500 mt-2 mb-6">Explore nosso catálogo e comece a estudar gratuitamente.</p><a href="cursos.php" class="inline-block bg-senai-blue text-white font-bold px-6 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">Ir para o Catálogo</a></div>
            <?php endif; ?>

        </div>

        <?php if ($total_inscritos > 0): ?>
        <div class="mt-10 bg-senai-blue rounded-2xl p-6 text-white text-center">
            <h3 class="font-extrabold text-lg mb-1">Quer aprender mais?</h3>
            <p class="text-blue-200 text-sm mb-4">Temos outros cursos disponíveis no catálogo.</p>
            <a href="cursos.php" class="inline-block bg-white text-senai-blue font-bold px-6 py-2.5 rounded-lg text-sm hover:bg-blue-50 transition">Ver todos os cursos</a>
        </div>
        <?php endif; ?>

    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-4 text-xs mt-auto">
        SENAI — Sistema EAD &nbsp;|&nbsp; © <?php echo date('Y'); ?> Todos os direitos reservados
    </footer>

<?php require_once "includes/footer.php"; ?>
