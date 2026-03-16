<?php
// ============================================
// Arquivo: admin/index.php
// Função: Dashboard do administrador e métricas
// ============================================

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$res_cursos = mysqli_query($conexao, "SELECT COUNT(*) as total FROM cursos");
$row_cursos = mysqli_fetch_assoc($res_cursos);
$total_cursos = $row_cursos['total'];

$res_modulos = mysqli_query($conexao, "SELECT COUNT(*) as total FROM modulos");
$row_modulos = mysqli_fetch_assoc($res_modulos);
$total_modulos = $row_modulos['total'];

$res_aulas = mysqli_query($conexao, "SELECT COUNT(*) as total FROM aulas");
$row_aulas = mysqli_fetch_assoc($res_aulas);
$total_aulas = $row_aulas['total'];

$res_inscricoes = mysqli_query($conexao, "SELECT COUNT(*) as total FROM inscricoes");
$row_inscricoes = mysqli_fetch_assoc($res_inscricoes);
$total_inscricoes = $row_inscricoes['total'];

$sql_ultimos = "SELECT * FROM cursos ORDER BY id DESC LIMIT 3";
$res_ultimos = mysqli_query($conexao, $sql_ultimos);

require_once "../includes/header.php";
?>
<title>Dashboard — Painel Admin | EAD SENAI</title>

<body class="bg-gray-100 min-h-screen flex">

    <?php require_once "includes/menu_admin.php"; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-500">Visão geral do sistema EAD</p>
            </div>
            <span class="text-xs text-gray-400"><?php setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese'); echo strftime('%A, %d de %B de %Y'); ?></span>
        </div>

        <div class="p-6 flex-1">

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-senai-blue">
                    <div class="flex items-center justify-between mb-2"><span class="text-2xl">📚</span><span class="text-xs text-gray-400 bg-blue-50 px-2 py-0.5 rounded">Total</span></div>
                    <p class="text-3xl font-extrabold text-senai-blue"><?php echo $total_cursos; ?></p>
                    <p class="text-sm text-gray-500 mt-1">Cursos cadastrados</p>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-senai-orange">
                    <div class="flex items-center justify-between mb-2"><span class="text-2xl">📦</span><span class="text-xs text-gray-400 bg-orange-50 px-2 py-0.5 rounded">Total</span></div>
                    <p class="text-3xl font-extrabold text-senai-orange"><?php echo $total_modulos; ?></p>
                    <p class="text-sm text-gray-500 mt-1">Módulos cadastrados</p>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-senai-red">
                    <div class="flex items-center justify-between mb-2"><span class="text-2xl">🎬</span><span class="text-xs text-gray-400 bg-red-50 px-2 py-0.5 rounded">Total</span></div>
                    <p class="text-3xl font-extrabold text-senai-red"><?php echo $total_aulas; ?></p>
                    <p class="text-sm text-gray-500 mt-1">Aulas cadastradas</p>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-senai-green">
                    <div class="flex items-center justify-between mb-2"><span class="text-2xl">👥</span><span class="text-xs text-gray-400 bg-green-50 px-2 py-0.5 rounded">Total</span></div>
                    <p class="text-3xl font-extrabold text-senai-green"><?php echo $total_inscricoes; ?></p>
                    <p class="text-sm text-gray-500 mt-1">Inscrições realizadas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="font-bold text-gray-700 mb-4 text-sm">Ações Rápidas</h2>
                    <div class="space-y-2">
                        <a href="curso_form.php" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition cursor-pointer">
                            <span class="w-8 h-8 bg-senai-blue rounded-lg flex items-center justify-center text-white text-sm">+</span>
                            <div><p class="text-sm font-semibold text-gray-700">Novo Curso</p><p class="text-xs text-gray-400">Cadastrar um curso</p></div>
                        </a>
                        <a href="modulo_form.php" class="flex items-center gap-3 p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition cursor-pointer">
                            <span class="w-8 h-8 bg-senai-orange rounded-lg flex items-center justify-center text-white text-sm">+</span>
                            <div><p class="text-sm font-semibold text-gray-700">Novo Módulo</p><p class="text-xs text-gray-400">Adicionar a um curso</p></div>
                        </a>
                        <a href="aula_form.php" class="flex items-center gap-3 p-3 bg-red-50 hover:bg-red-100 rounded-lg transition cursor-pointer">
                            <span class="w-8 h-8 bg-senai-red rounded-lg flex items-center justify-center text-white text-sm">+</span>
                            <div><p class="text-sm font-semibold text-gray-700">Nova Aula</p><p class="text-xs text-gray-400">Adicionar a um módulo</p></div>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4"><h2 class="font-bold text-gray-700 text-sm">Últimos Cursos Cadastrados</h2><a href="cursos.php" class="text-xs text-senai-blue underline">Ver todos</a></div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 uppercase"><th class="text-left pb-2 font-semibold">Curso</th><th class="text-center pb-2 font-semibold">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (mysqli_num_rows($res_ultimos) > 0): ?>
                                <?php while ($curso = mysqli_fetch_assoc($res_ultimos)): ?>
                                <tr>
                                    <td class="py-2.5 font-medium text-gray-700"><?php echo htmlspecialchars($curso['titulo']); ?></td>
                                    <td class="py-2.5 text-center">
                                        <?php if ($curso['ativo'] == 1): ?>
                                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Ativo</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="py-4 text-center text-gray-500">Nenhum curso cadastrado ainda.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>

<?php require_once "../includes/footer.php"; ?>
