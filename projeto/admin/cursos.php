<?php
// ============================================
// Arquivo: admin/cursos.php
// Função: Listar cursos cadastrados no sistema
// ============================================

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$sql = "
    SELECT 
        c.*,
        (SELECT COUNT(*) FROM modulos m WHERE m.curso_id = c.id) as total_modulos,
        (SELECT COUNT(*) FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = c.id) as total_aulas,
        (SELECT COUNT(*) FROM inscricoes i WHERE i.curso_id = c.id) as total_inscricoes
    FROM cursos c
    ORDER BY c.id DESC
";
$resultado = mysqli_query($conexao, $sql);

require_once "../includes/header.php";
?>
<title>Gerenciar Cursos — Admin | EAD SENAI</title>

<body class="bg-gray-100 min-h-screen flex">

    <?php require_once "includes/menu_admin.php"; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Cursos</h1>
                <p class="text-sm text-gray-500">Cadastre, edite e organize os cursos da plataforma</p>
            </div>
            <a href="curso_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">+ Novo Curso</a>
        </div>

        <div class="p-6 flex-1">

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'excluido'): ?>
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="font-bold text-base">✓</span><span>Curso excluído com sucesso!</span></div>
                <?php elseif ($_GET['msg'] == 'salvo'): ?>
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="font-bold text-base">✓</span><span>Curso salvo com sucesso!</span></div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">#</th>
                            <th class="px-4 py-3 text-left">Curso</th>
                            <th class="px-4 py-3 text-center">Módulos</th>
                            <th class="px-4 py-3 text-center">Aulas</th>
                            <th class="px-4 py-3 text-center">Inscrições</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Cadastrado em</th>
                            <th class="px-4 py-3 text-center w-48">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while ($curso = mysqli_fetch_assoc($resultado)): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-400 font-mono text-xs"><?php echo $curso['id']; ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold"><?php echo strtoupper(substr($curso['titulo'], 0, 1)); ?></div>
                                        <div>
                                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($curso['titulo']); ?></p>
                                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[200px]"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?php echo $curso['total_modulos']; ?></td>
                                <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?php echo $curso['total_aulas']; ?></td>
                                <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?php echo $curso['total_inscricoes']; ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($curso['ativo'] == 1): ?>
                                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Ativo</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray-400"><?php echo date('d/m/Y', strtotime($curso['criado_em'])); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <a href="modulos.php?curso_id=<?php echo $curso['id']; ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver Módulos">📦 Módulos</a>
                                        <a href="curso_form.php?id=<?php echo $curso['id']; ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏ Editar</a>
                                        <a href="curso_delete.php?id=<?php echo $curso['id']; ?>" onclick="return confirm('ATENÇÃO: Excluir este curso também excluirá TODOS os módulos e aulas associados a ele. Deseja mesmo excluir?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" title="Excluir">🗑 Excluir</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Nenhum curso cadastrado. <a href="curso_form.php" class="text-senai-blue underline">Cadastre o primeiro curso</a>.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

<?php require_once "../includes/footer.php"; ?>
