<?php
// ============================================
// Arquivo: admin/aulas.php
// Função: Listar as aulas de um módulo específico
// ============================================

session_start();

// Verifica se está logado e se é admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$curso_id = "";
$modulo_id = "";
$curso_titulo = "Curso Desconhecido";
$modulo_titulo = "Módulo Desconhecido";

// Verifica se recebeu os IDs de curso e módulo
if (isset($_GET['modulo_id']) && !empty($_GET['modulo_id']) && isset($_GET['curso_id']) && !empty($_GET['curso_id'])) {
    $modulo_id = $_GET['modulo_id'];
    $curso_id = $_GET['curso_id'];

    // Busca informações do curso
    $sql_curso = "SELECT titulo FROM cursos WHERE id = '$curso_id'";
    $res_curso = mysqli_query($conexao, $sql_curso);
    if ($curso = mysqli_fetch_assoc($res_curso)) {
        $curso_titulo = $curso['titulo'];
    }

    // Busca informações do módulo
    $sql_modulo = "SELECT titulo FROM modulos WHERE id = '$modulo_id'";
    $res_modulo = mysqli_query($conexao, $sql_modulo);
    if ($modulo = mysqli_fetch_assoc($res_modulo)) {
        $modulo_titulo = $modulo['titulo'];
    }
}
else {
    // Redireciona para a listagem se faltar parâmetro
    if (isset($_GET['curso_id'])) {
        header("Location: modulos.php?curso_id=" . $_GET['curso_id']);
    }
    else {
        header("Location: cursos.php");
    }
    exit;
}

// Obter as aulas deste módulo
$sql_aulas = "SELECT * FROM aulas WHERE modulo_id = '$modulo_id' ORDER BY ordem ASC";
$resultado = mysqli_query($conexao, $sql_aulas);

// Pega a próxima ordem possível
$proxima_ordem = mysqli_num_rows($resultado) + 1;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Aulas — Admin | EAD SENAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { senai: { red:'#C0392B', blue:'#34679A', 'blue-dark':'#2C5A85', orange:'#E67E22', green:'#27AE60' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .nav-link { display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:6px; font-size:13px; cursor:pointer; transition:background .15s; color:#cbd5e1; }
        .nav-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .nav-link.active { background:rgba(255,255,255,.15); color:#fff; font-weight:600; }
        .form-input { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:10px 14px; font-size:14px; outline:none; transition:border .15s; }
        .form-input:focus { border-color:#34679A; box-shadow:0 0 0 3px rgba(52,103,154,.15); }
        .form-label { display:block; font-size:12px; font-weight:600; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <?php require_once "includes/menu_admin.php"; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                    <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                    <a href="curso_form.php?id=<?php echo $curso_id; ?>" class="hover:text-senai-blue"><?php echo htmlspecialchars($curso_titulo); ?></a> ›
                    <a href="modulos.php?curso_id=<?php echo $curso_id; ?>" class="hover:text-senai-blue">Módulos</a> ›
                    <span class="text-gray-700 font-semibold truncate max-w-[150px] inline-block align-bottom"><?php echo htmlspecialchars($modulo_titulo); ?></span>
                </div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Aulas</h1>
            </div>
            <a href="aula_form.php?modulo_id=<?php echo $modulo_id; ?>&curso_id=<?php echo $curso_id; ?>" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition">+ Nova Aula</a>
        </div>

        <div class="p-6 flex-1">
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
            <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">✓</span>
                <span>Aula excluída com sucesso!</span>
            </div>
            <?php
elseif (isset($_GET['msg']) && $_GET['msg'] == 'salvo'): ?>
            <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">✓</span>
                <span>Aula salva com sucesso!</span>
            </div>
            <?php
endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- LISTA DE AULAS -->
                <div class="space-y-3">
                    <h2 class="font-bold text-gray-700 text-sm">Aulas do Módulo: <?php echo htmlspecialchars($modulo_titulo); ?></h2>

                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($aula = mysqli_fetch_assoc($resultado)): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative group hover:border-senai-blue/30 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-senai-red/10 rounded-lg flex items-center justify-center text-senai-red text-sm font-bold flex-shrink-0">
                                    <?php echo $aula['ordem']; ?>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="font-semibold text-gray-800 text-sm truncate"><?php echo htmlspecialchars($aula['titulo']); ?></p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                                        <?php if (!empty($aula['duracao'])): ?>
                                        <span>⏱ <?php echo htmlspecialchars($aula['duracao']); ?></span>
                                        <span>&middot;</span>
                                        <?php
        endif; ?>
                                        <?php if (!empty($aula['video_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($aula['video_url']); ?>" target="_blank" class="text-blue-500 hover:text-blue-700 underline flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                              <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z"/>
                                            </svg>
                                            Ver vídeo
                                        </a>
                                        <?php
        else: ?>
                                        <span class="text-gray-400 italic">Sem vídeo vinculado</span>
                                        <?php
        endif; ?>
                                    </div>
                                </div>
                                <div class="flex gap-1.5 opacity-100 lg:opacity-0 group-hover:opacity-100 transition">
                                    <a href="aula_form.php?id=<?php echo $aula['id']; ?>&modulo_id=<?php echo $modulo_id; ?>&curso_id=<?php echo $curso_id; ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏</a>
                                    <a href="aula_delete.php?id=<?php echo $aula['id']; ?>&modulo_id=<?php echo $modulo_id; ?>&curso_id=<?php echo $curso_id; ?>" onclick="return confirm('Tem certeza que deseja excluir esta aula?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" title="Excluir">🗑</a>
                                </div>
                            </div>
                        </div>
                        <?php
    endwhile; ?>
                    <?php
else: ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                            <p class="text-gray-500 text-sm">Nenhuma aula cadastrada para este módulo.</p>
                            <p class="text-gray-400 text-xs mt-1">Utilize o formulário ao lado para cadastrar a primeira aula.</p>
                        </div>
                    <?php
endif; ?>

                </div>

                <!-- FORMULÁRIO RÁPIDO -->
                <div>
                    <div class="bg-white rounded-xl shadow-sm p-5 sticky top-6">
                        <h2 class="font-bold text-gray-700 text-sm mb-4">Adicionar Nova Aula</h2>
                        <form action="aula_form.php" method="post">
                            <input type="hidden" name="modulo_id" value="<?php echo $modulo_id; ?>">
                            <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                            
                            <div class="mb-4">
                                <label class="form-label">Título da Aula *</label>
                                <input type="text" name="titulo" class="form-input" placeholder="Ex: Introdução às Tabelas HTML" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">URL do Vídeo</label>
                                <input type="url" name="video_url" class="form-input" placeholder="https://www.youtube.com/embed/...">
                                <p class="text-xs text-gray-400 mt-1">Preferencialmente a URL de incorporação (embed) do YouTube/Vimeo.</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Duração</label>
                                <input type="text" name="duracao" class="form-input" placeholder="Ex: 12:30">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Descrição (opcional)</label>
                                <textarea name="descricao" rows="3" class="form-input resize-none" placeholder="Breve resumo da aula..."></textarea>
                            </div>
                            
                            <div class="mb-5">
                                <label class="form-label">Ordem</label>
                                <input type="number" name="ordem" class="form-input" value="<?php echo $proxima_ordem; ?>" min="1" required>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">
                                    Salvar Aula
                                </button>
                                <a href="modulos.php?curso_id=<?php echo $curso_id; ?>" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
