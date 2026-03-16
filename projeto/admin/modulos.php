<?php
// ============================================
// Arquivo: admin/modulos.php
// Função: Listar os módulos de um curso específico
// ============================================

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$curso_id = "";
$curso_titulo = "Curso Desconhecido";

if (isset($_GET['curso_id']) && !empty($_GET['curso_id'])) {
    $curso_id = $_GET['curso_id'];

    $sql_curso = "SELECT titulo FROM cursos WHERE id = '$curso_id'";
    $res_curso = mysqli_query($conexao, $sql_curso);
    if ($curso = mysqli_fetch_assoc($res_curso)) {
        $curso_titulo = $curso['titulo'];
    }
} else {
    header("Location: cursos.php");
    exit;
}

$sql_modulos = "
    SELECT m.*, 
    (SELECT COUNT(*) FROM aulas a WHERE a.modulo_id = m.id) as total_aulas
    FROM modulos m 
    WHERE m.curso_id = '$curso_id' 
    ORDER BY m.ordem ASC
";
$resultado = mysqli_query($conexao, $sql_modulos);

$proxima_ordem = mysqli_num_rows($resultado) + 1;

require_once "../includes/header.php";
?>
<title>Gerenciar Módulos — Admin | EAD SENAI</title>

<body class="bg-gray-100 min-h-screen flex">

    <?php require_once "includes/menu_admin.php"; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                    <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                    <a href="curso_form.php?id=<?php echo $curso_id; ?>" class="hover:text-senai-blue"><?php echo htmlspecialchars($curso_titulo); ?></a> ›
                    <span class="text-gray-700 font-semibold">Módulos</span>
                </div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Módulos</h1>
            </div>
            <a href="modulo_form.php?curso_id=<?php echo $curso_id; ?>" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition">+ Novo Módulo</a>
        </div>

        <div class="p-6 flex-1">
            
            <?php if (isset($_GET['msg'])):
                if ($_GET['msg'] == 'excluido'): ?>
                    <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="font-bold text-base">✓</span><span>Módulo excluído com sucesso!</span></div>
                <?php elseif ($_GET['msg'] == 'salvo'): ?>
                    <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="font-bold text-base">✓</span><span>Módulo salvo com sucesso!</span></div>
            <?php endif; endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="space-y-3">
                    <h2 class="font-bold text-gray-700 text-sm">Módulos do Curso</h2>

                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($modulo = mysqli_fetch_assoc($resultado)): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 bg-senai-blue rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"><?php echo $modulo['ordem']; ?></div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($modulo['titulo']); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo $modulo['total_aulas']; ?> aulas cadastradas</p>
                                </div>
                                <div class="flex gap-1.5 opacity-100">
                                    <a href="aulas.php?modulo_id=<?php echo $modulo['id']; ?>&curso_id=<?php echo $curso_id; ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition">🎬 Aulas</a>
                                    <a href="modulo_form.php?id=<?php echo $modulo['id']; ?>&curso_id=<?php echo $curso_id; ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏</a>
                                    <a href="modulo_delete.php?id=<?php echo $modulo['id']; ?>&curso_id=<?php echo $curso_id; ?>" onclick="return confirm('Excluir este módulo também excluirá as aulas dele. Deseja continuar?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" title="Excluir">🗑</a>
                                </div>
                            </div>
                            <?php if (!empty($modulo['descricao'])): ?>
                                <p class="text-xs text-gray-500 mt-2 p-2 bg-gray-50 rounded italic border-l-2 border-senai-blue"><?php echo htmlspecialchars($modulo['descricao']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center"><p class="text-gray-500 text-sm">Nenhum módulo cadastrado para este curso.</p><p class="text-gray-400 text-xs mt-1">Utilize o formulário ao lado para cadastrar o primeiro.</p></div>
                    <?php endif; ?>

                </div>

                <div>
                    <div class="bg-white rounded-xl shadow-sm p-5 sticky top-6">
                        <h2 class="font-bold text-gray-700 text-sm mb-4">Adicionar Novo Módulo</h2>
                        <form action="modulo_form.php" method="post">
                            <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                            
                            <div class="mb-4">
                                <label class="form-label">Título do Módulo *</label>
                                <input type="text" name="titulo" class="form-input" placeholder="Ex: Introdução ao HTML" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Descrição (opcional)</label>
                                <textarea name="descricao" rows="3" class="form-input resize-none" placeholder="Breve descrição do módulo..."></textarea>
                            </div>
                            
                            <div class="mb-5">
                                <label class="form-label">Ordem</label>
                                <input type="number" name="ordem" class="form-input" value="<?php echo $proxima_ordem; ?>" min="1" required>
                            </div>
                            
                            <div class="flex gap-2"><button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">Salvar Módulo</button><a href="cursos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Voltar aos Cursos</a></div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

<?php require_once "../includes/footer.php"; ?>
