<?php
// ============================================
// Arquivo: curso.php
// Função: Exibir módulos e aulas de um curso
// ============================================

session_start();
require_once "logado.php";
require_once "conexao.php";

$usuario_id = $_SESSION["usuario_id"];
$nome_aluno = explode(" ", $_SESSION["usuario_nome"])[0];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: meus_cursos.php");
    exit;
}

$curso_id = (int)$_GET['id'];

$sql_verifica = "SELECT id FROM inscricoes WHERE usuario_id = $usuario_id AND curso_id = $curso_id";
$res_verifica = mysqli_query($conexao, $sql_verifica);

if (mysqli_num_rows($res_verifica) == 0) {
    header("Location: cursos.php?aviso=nao_inscrito");
    exit;
}

$sql_curso = "
    SELECT 
        c.*,
        (SELECT COUNT(*) FROM modulos m WHERE m.curso_id = c.id) as total_modulos,
        (SELECT COUNT(*) FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = c.id) as total_aulas
    FROM cursos c 
    WHERE c.id = $curso_id AND c.ativo = 1
";
$res_curso = mysqli_query($conexao, $sql_curso);

if (mysqli_num_rows($res_curso) == 0) {
    header("Location: meus_cursos.php?erro=curso_indisponivel");
    exit;
}

$curso = mysqli_fetch_assoc($res_curso);

$sql_modulos = "SELECT * FROM modulos WHERE curso_id = $curso_id ORDER BY ordem ASC";
$res_modulos = mysqli_query($conexao, $sql_modulos);

$modulos = [];
while ($modulo = mysqli_fetch_assoc($res_modulos)) {
    $modulo_id = $modulo['id'];
    $sql_aulas = "SELECT * FROM aulas WHERE modulo_id = $modulo_id ORDER BY ordem ASC";
    $res_aulas = mysqli_query($conexao, $sql_aulas);

    $aulas = [];
    while ($aula = mysqli_fetch_assoc($res_aulas)) {
        $aulas[] = $aula;
    }

    $modulo['aulas'] = $aulas;
    $modulos[] = $modulo;
}

$progresso = 0;
$aulas_concluidas = 0;

require_once "includes/header.php";
?>
<title><?php echo htmlspecialchars($curso['titulo']); ?> — EAD SENAI</title>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php require_once "includes/menu_aluno.php"; ?>

    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                <a href="meus_cursos.php" class="hover:text-senai-blue">Meus Cursos</a>
                <span>›</span>
                <span class="text-gray-700 font-semibold truncate max-w-[200px] inline-block align-bottom"><?php echo htmlspecialchars($curso['titulo']); ?></span>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-4">
                    <?php if (!empty($curso['capa'])): ?>
                        <div class="w-14 h-14 rounded-lg overflow-hidden flex flex-shrink-0 relative border border-gray-100"><img src="uploads/capas/<?php echo htmlspecialchars($curso['capa']); ?>" class="w-full h-full object-cover"></div>
                    <?php else: ?>
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-14 h-14 rounded-lg flex items-center justify-center flex-shrink-0"><span class="text-3xl text-white font-bold opacity-50"><?php echo strtoupper(substr($curso['titulo'], 0, 1)); ?></span></div>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-xl font-extrabold text-gray-800"><?php echo htmlspecialchars($curso['titulo']); ?></h1>
                        <div class="flex gap-4 text-xs text-gray-500 mt-1"><span>📚 <?php echo $curso['total_modulos']; ?> módulos</span><span>🎬 <?php echo $curso['total_aulas']; ?> aulas</span></div>
                    </div>
                </div>
                <div class="min-w-48 hidden sm:block">
                    <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Progresso geral</span><span class="font-semibold text-senai-green">0%</span></div>
                    <div class="bg-gray-200 rounded-full h-3"><div class="bg-senai-green h-3 rounded-full" style="width:0%"></div></div>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-6xl mx-auto px-6 py-6 flex gap-6 flex-1 w-full">

        <div class="flex-1 space-y-4">
            <?php if (count($modulos) > 0): ?>
                <?php foreach ($modulos as $index => $modulo): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="modulo-header flex items-center justify-between px-5 py-4 bg-blue-50 border-l-4 border-senai-blue">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-sm font-bold"><?php echo $modulo['ordem']; ?></div>
                            <div>
                                <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($modulo['titulo']); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo count($modulo['aulas']); ?> aulas</p>
                            </div>
                        </div>
                    </div>
                    <div class="modulo-body divide-y divide-gray-100">
                        <?php if (count($modulo['aulas']) > 0): ?>
                            <?php foreach ($modulo['aulas'] as $aula): ?>
                            <div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-senai-blue rounded-full flex items-center justify-center flex-shrink-0"><span class="text-white text-xs">▶</span></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($aula['titulo']); ?></p>
                                    <?php if (!empty($aula['duracao'])): ?><p class="text-xs text-gray-400">⏱ <?php echo htmlspecialchars($aula['duracao']); ?></p><?php endif; ?>
                                </div>
                                <a href="aula.php?id=<?php echo $aula['id']; ?>&curso_id=<?php echo $curso_id; ?>" class="bg-gray-100 text-gray-700 font-medium text-xs px-4 py-1.5 rounded-lg hover:bg-senai-blue hover:text-white transition whitespace-nowrap">Acessar Aula</a>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="px-5 py-4 text-center"><p class="text-sm text-gray-500">Nenhuma aula cadastrada neste módulo.</p></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100"><span class="text-5xl mb-3 block">📭</span><h3 class="text-lg font-bold text-gray-800">Este curso ainda não possui conteúdo</h3><p class="text-sm text-gray-500 mt-2 mb-6">Os módulos estão sendo preparados. Volte em breve!</p></div>
            <?php endif; ?>
        </div>

        <aside class="w-64 flex-shrink-0 hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-20">
                <h4 class="font-bold text-gray-700 text-sm mb-3">Navegação do Curso</h4>
                <div class="max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                    <ul class="space-y-1 text-xs">
                        <?php foreach ($modulos as $modulo): ?>
                            <li class="font-bold text-gray-700 mt-3 mb-1 first:mt-0"><?php echo htmlspecialchars($modulo['titulo']); ?></li>
                            <?php foreach ($modulo['aulas'] as $aula): ?>
                                <li class="text-gray-500 pl-3 line-clamp-1 py-0.5 hover:text-senai-blue cursor-pointer transition"><a href="aula.php?id=<?php echo $aula['id']; ?>&curso_id=<?php echo $curso_id; ?>">▶ <?php echo htmlspecialchars($aula['titulo']); ?></a></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </aside>

    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-4 text-xs mt-auto">
        SENAI — Sistema EAD &nbsp;|&nbsp; © <?php echo date('Y'); ?> Todos os direitos reservados
    </footer>

<?php require_once "includes/footer.php"; ?>
