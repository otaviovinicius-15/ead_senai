<?php
// ============================================
// Arquivo: cursos.php
// Função: Catálogo de cursos para o aluno se inscrever
// ============================================

session_start();
require_once "logado.php";
require_once "conexao.php";

$usuario_id = $_SESSION["usuario_id"];
$msg = "";

if (isset($_GET['inscrever']) && !empty($_GET['inscrever'])) {
    $curso_id = (int)$_GET['inscrever'];

    $sql_verifica = "SELECT id FROM inscricoes WHERE usuario_id = $usuario_id AND curso_id = $curso_id";
    $res_verifica = mysqli_query($conexao, $sql_verifica);

    if (mysqli_num_rows($res_verifica) == 0) {
        $sql_inscricao = "INSERT INTO inscricoes (usuario_id, curso_id) VALUES ($usuario_id, $curso_id)";
        if (mysqli_query($conexao, $sql_inscricao)) {
            $msg = "sucesso";
        }
    }
}

$sql_cursos = "
    SELECT 
        c.*,
        (SELECT COUNT(*) FROM modulos m WHERE m.curso_id = c.id) as total_modulos,
        (SELECT COUNT(*) FROM aulas a INNER JOIN modulos m ON a.modulo_id = m.id WHERE m.curso_id = c.id) as total_aulas,
        (SELECT id FROM inscricoes i WHERE i.usuario_id = $usuario_id AND i.curso_id = c.id LIMIT 1) as inscrito
    FROM cursos c
    WHERE c.ativo = 1
    ORDER BY c.id DESC
";
$resultado = mysqli_query($conexao, $sql_cursos);

require_once "includes/header.php";
?>
<title>Catálogo de Cursos — EAD SENAI</title>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php require_once "includes/menu_aluno.php"; ?>

    <div class="bg-white border-b border-gray-200 px-6 py-5">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl font-extrabold text-gray-800">Catálogo de Cursos</h1>
            <p class="text-sm text-gray-500 mt-1">Escolha um curso, inscreva-se e comece a aprender agora mesmo.</p>
        </div>
    </div>

    <?php if ($msg == "sucesso"): ?>
    <div class="max-w-6xl mx-auto px-6 pt-5">
        <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 flex items-center gap-2 text-sm"><span class="font-bold text-lg">✓</span><span>Inscrição realizada com sucesso! Acesse <a href="meus_cursos.php" class="underline font-semibold">Meus Cursos</a> para começar.</span></div>
    </div>
    <?php endif; ?>

    <main class="max-w-6xl mx-auto px-6 py-8 flex-1">
        
        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <?php while ($curso = mysqli_fetch_assoc($resultado)): ?>
                    <?php $inscrito = !empty($curso['inscrito']); ?>

                    <div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden flex flex-col <?php echo $inscrito ? 'border-2 border-green-400' : ''; ?>">
                        
                        <div class="relative">
                            <?php if (!empty($curso['capa'])): ?>
                                <div class="h-40 overflow-hidden relative"><div class="absolute inset-0 bg-black/20 z-10"></div><img src="uploads/capas/<?php echo htmlspecialchars($curso['capa']); ?>" alt="Capa" class="w-full h-full object-cover"></div>
                            <?php else: ?>
                                <div class="bg-gradient-to-br from-blue-500 to-blue-700 h-40 flex items-center justify-center"><span class="text-6xl font-bold text-white/50"><?php echo strtoupper(substr($curso['titulo'], 0, 1)); ?></span></div>
                            <?php endif; ?>
                            
                            <?php if ($inscrito): ?>
                                <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full z-20 shadow">✓ Inscrito</span>
                            <?php endif; ?>
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-2 mb-2"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">Curso Livre</span><span class="text-xs text-gray-400 font-medium"><?php echo $curso['total_modulos']; ?> módulos · <?php echo $curso['total_aulas']; ?> aulas</span></div>
                            <h3 class="font-bold text-gray-800 text-base mb-2"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                            <p class="text-sm text-gray-500 mb-5 flex-1 line-clamp-3"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                            
                            <?php if ($inscrito): ?>
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Status</span><span class="text-senai-green font-semibold">Iniciado</span></div>
                                    <div class="bg-gray-200 rounded-full h-2"><div class="bg-senai-green h-2 rounded-full" style="width:10%"></div></div>
                                </div>
                                <a href="curso.php?id=<?php echo $curso['id']; ?>" class="block w-full bg-senai-green text-white text-sm font-semibold py-2.5 rounded-lg text-center hover:bg-green-600 transition">Continuar Curso →</a>
                            <?php else: ?>
                                <a href="cursos.php?inscrever=<?php echo $curso['id']; ?>" class="block w-full bg-senai-blue text-white text-sm font-semibold py-2.5 rounded-lg text-center hover:bg-senai-blue-dark transition">Inscrever-se Grátis</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>
        <?php else: ?>
            <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100"><span class="text-6xl mb-4 block">📭</span><h3 class="text-lg font-bold text-gray-800 mb-2">Nenhum curso disponível</h3><p class="text-gray-500 text-sm">Não há cursos ativos no catálogo no momento. Volte mais tarde.</p></div>
        <?php endif; ?>

    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-4 text-xs mt-auto">
        SENAI — Sistema EAD &nbsp;|&nbsp; © 2025 Todos os direitos reservados
    </footer>

<?php require_once "includes/footer.php"; ?>
