<?php
// ============================================
// Arquivo: aula.php
// Função: Exibir o player da aula e navegação
// ============================================

session_start();
require_once "logado.php";
require_once "conexao.php";

$usuario_id = $_SESSION["usuario_id"];

// Validação dos parâmetros GET
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['curso_id']) || empty($_GET['curso_id'])) {
    header("Location: meus_cursos.php");
    exit;
}

$aula_id = (int)$_GET['id'];
$curso_id = (int)$_GET['curso_id'];

// 1. Verifica se o aluno está inscrito no curso
$sql_inscricao = "SELECT id FROM inscricoes WHERE usuario_id = $usuario_id AND curso_id = $curso_id";
$res_inscricao = mysqli_query($conexao, $sql_inscricao);
if (mysqli_num_rows($res_inscricao) == 0) {
    header("Location: cursos.php?aviso=nao_inscrito");
    exit;
}

// 2. Busca os dados da aula atual
$sql_aula = "SELECT a.*, m.titulo as modulo_titulo, m.curso_id FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE a.id = $aula_id";
$res_aula = mysqli_query($conexao, $sql_aula);
if (mysqli_num_rows($res_aula) == 0) {
    header("Location: curso.php?id=$curso_id&erro=aula_invalida");
    exit;
}
$aula = mysqli_fetch_assoc($res_aula);

// 3. Busca dados do curso e progresso (estático por enquanto)
$sql_curso = "SELECT titulo FROM cursos WHERE id = $curso_id";
$res_curso = mysqli_query($conexao, $sql_curso);
$curso = mysqli_fetch_assoc($res_curso);
$progresso = 33; // Valor estático

// 4. Busca todos os módulos e aulas do curso para a sidebar
$sql_modulos = "SELECT * FROM modulos WHERE curso_id = $curso_id ORDER BY ordem ASC";
$res_modulos = mysqli_query($conexao, $sql_modulos);
$modulos_sidebar = [];
while ($modulo = mysqli_fetch_assoc($res_modulos)) {
    $modulo_id_sidebar = $modulo['id'];
    $sql_aulas_sidebar = "SELECT id, titulo, duracao FROM aulas WHERE modulo_id = $modulo_id_sidebar ORDER BY ordem ASC";
    $res_aulas_sidebar = mysqli_query($conexao, $sql_aulas_sidebar);
    $aulas_arr = [];
    while ($aula_sidebar = mysqli_fetch_assoc($res_aulas_sidebar)) {
        $aulas_arr[] = $aula_sidebar;
    }
    $modulo['aulas'] = $aulas_arr;
    $modulos_sidebar[] = $modulo;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($aula['titulo']); ?> — EAD SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col">

    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-4">
            <a href="cursos.php" class="flex items-center gap-1.5 text-white font-extrabold text-base">🎓 EAD SENAI</a>
            <span class="text-gray-600">/</span>
            <a href="curso.php?id=<?php echo $curso_id; ?>" class="text-gray-400 hover:text-white text-sm transition"><?php echo htmlspecialchars($curso['titulo']); ?></a>
            <div class="flex-1"></div>
            <a href="meus_cursos.php" class="text-gray-400 hover:text-white text-xs transition">← Voltar para Meus Cursos</a>
            <a href="logout.php" class="bg-senai-red text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-red-700 transition ml-2">Sair</a>
        </div>
    </nav>

    <div class="flex flex-1 max-w-7xl mx-auto w-full">
        <aside class="w-80 bg-gray-800 border-r border-gray-700 flex-shrink-0 overflow-y-auto hidden lg:block" style="height: calc(100vh - 44px); position: sticky; top: 44px;">
            <div class="p-4">
                <h3 class="text-white font-bold text-sm mb-1"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex-1 bg-gray-700 rounded-full h-1.5"><div class="bg-senai-green h-1.5 rounded-full" style="width:<?php echo $progresso; ?>%"></div></div>
                    <span class="text-xs text-gray-400"><?php echo $progresso; ?>%</span>
                </div>

                <?php foreach ($modulos_sidebar as $modulo_item): ?>
                <div class="mb-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-white mb-2 uppercase tracking-wide">
                        <span class="w-5 h-5 bg-senai-blue rounded-full flex items-center justify-center text-xs"><?php echo $modulo_item['ordem']; ?></span>
                        <?php echo htmlspecialchars($modulo_item['titulo']); ?>
                    </div>
                    <ul class="space-y-1 pl-2">
                        <?php foreach ($modulo_item['aulas'] as $aula_item): ?>
                            <?php
                            $is_current = ($aula_item['id'] == $aula_id);
                            $link_class = $is_current ? 'bg-senai-blue text-white font-semibold' : 'text-gray-400 hover:bg-gray-700';
                            ?>
                            <a href="aula.php?id=<?php echo $aula_item['id']; ?>&curso_id=<?php echo $curso_id; ?>" class="flex items-center gap-2 py-1.5 px-2 rounded text-xs cursor-pointer <?php echo $link_class; ?>">
                                <span class="w-4 h-4 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0"><?php echo $is_current ? '▶' : '○'; ?></span>
                                <span><?php echo htmlspecialchars($aula_item['titulo']); ?></span>
                                <?php if($aula_item['duracao']): ?><span class="ml-auto text-gray-500"><?php echo $aula_item['duracao']; ?></span><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto">
            <div class="bg-black aspect-video flex items-center justify-center w-full">
                <?php if (!empty($aula['video_url'])): ?>
                    <iframe src="<?php echo htmlspecialchars($aula['video_url']); ?>" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <?php else: ?>
                    <div class="text-center text-gray-500 p-8">
                        <p>Vídeo indisponível para esta aula.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white p-6 lg:p-8">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">Módulo <?php echo htmlspecialchars($aula['modulo_titulo']); ?></span>
                            <?php if($aula['duracao']): ?><span class="text-gray-400 text-xs">⏱ <?php echo htmlspecialchars($aula['duracao']); ?></span><?php endif; ?>
                        </div>
                        <h1 class="text-2xl font-extrabold text-gray-800"><?php echo htmlspecialchars($aula['titulo']); ?></h1>
                    </div>
                </div>

                <?php if (!empty($aula['descricao'])): ?>
                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-4 mb-6">
                    <?php echo nl2br(htmlspecialchars($aula['descricao'])); ?>
                </div>
                <?php endif; ?>

                <div class="flex items-center gap-3 flex-wrap">
                    <button type="button" class="flex items-center gap-1.5 bg-senai-green text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-green-600 transition shadow">
                        ✓ Marcar como Concluída
                    </button>
                    <a href="curso.php?id=<?php echo $curso_id; ?>" class="ml-auto bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Voltar aos Módulos</a>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
