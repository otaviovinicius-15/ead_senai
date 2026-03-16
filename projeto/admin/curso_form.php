<?php
// ============================================
// Arquivo: admin/curso_form.php
// Função: Formulário para cadastrar ou editar cursos
// ============================================

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$id = "";
$titulo = "";
$descricao = "";
$capa = "";
$ativo = 1;
$acao = "Cadastrar Novo Curso";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_busca = "SELECT * FROM cursos WHERE id = '$id'";
    $resultado = mysqli_query($conexao, $sql_busca);

    if ($curso = mysqli_fetch_assoc($resultado)) {
        $titulo = $curso['titulo'];
        $descricao = $curso['descricao'];
        $capa = $curso['capa'];
        $ativo = $curso['ativo'];
        $acao = "Editar Curso";
    }
}

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_post = $_POST['id'];
    $titulo_post = $_POST['titulo'];
    $descricao_post = $_POST['descricao'];
    $ativo_post = $_POST['ativo'];

    $capa_post = $capa;

    if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($extensao, $extensoes_permitidas)) {
            $novo_nome = uniqid() . "." . $extensao;
            $destino = "../uploads/capas/" . $novo_nome;

            if (!is_dir("../uploads/capas")) {
                mkdir("../uploads/capas", 0777, true);
            }

            if (move_uploaded_file($_FILES['capa']['tmp_name'], $destino)) {
                $capa_post = $novo_nome;
            }
            else {
                $erro = "Falha ao fazer o upload da imagem.";
            }
        }
        else {
            $erro = "Formato de imagem inválido. Use JPG, PNG ou WEBP.";
        }
    }

    if (empty($erro)) {
        if (!empty($id_post)) {
            $sql = "UPDATE cursos SET titulo = '$titulo_post', descricao = '$descricao_post', capa = '$capa_post', ativo = '$ativo_post' WHERE id = '$id_post'";
        }
        else {
            $sql = "INSERT INTO cursos (titulo, descricao, capa, ativo) VALUES ('$titulo_post', '$descricao_post', '$capa_post', '$ativo_post')";
        }

        if (mysqli_query($conexao, $sql)) {
            header("Location: cursos.php?msg=salvo");
            exit;
        }
        else {
            $erro = "Erro ao salvar no banco de dados: " . mysqli_error($conexao);
        }
    }
}

require_once "../includes/header.php";
?>
<title><?php echo $acao; ?> — Admin | EAD SENAI</title>
<body class="bg-gray-100 min-h-screen flex">

    <?php require_once "includes/menu_admin.php"; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">

        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1"><a href="cursos.php" class="hover:text-senai-blue">Cursos</a><span>›</span><span class="text-gray-700 font-semibold"><?php echo $acao; ?></span></div>
            <div class="flex items-center justify-between"><h1 class="text-xl font-extrabold text-gray-800"><?php echo $acao; ?></h1><a href="cursos.php" class="text-sm text-gray-500 hover:text-senai-blue flex items-center gap-1 transition">← Voltar para Cursos</a></div>
        </div>

        <div class="p-6 flex-1">
            
            <?php if (!empty($erro)): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="text-red-500 font-bold text-base">⚠</span><span><?php echo $erro; ?></span></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6">

                        <form action="curso_form.php" method="post" enctype="multipart/form-data">

                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                            <div class="mb-5">
                                <label class="form-label">Título do Curso *</label>
                                <input type="text" name="titulo" class="form-input" placeholder="Ex: HTML e CSS do Zero" value="<?php echo htmlspecialchars($titulo); ?>" required>
                                <p class="text-xs text-gray-400 mt-1">Use um título claro e direto. Máx. 150 caracteres.</p>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Descrição *</label>
                                <textarea name="descricao" rows="4" class="form-input resize-none" placeholder="Descreva o curso, o que o aluno vai aprender..." required><?php echo htmlspecialchars($descricao); ?></textarea>
                                <p class="text-xs text-gray-400 mt-1">Seja claro sobre o conteúdo e o público-alvo do curso.</p>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Imagem de Capa</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-senai-blue transition cursor-pointer bg-gray-50 relative">
                                    <?php if (!empty($capa)): ?>
                                        <div class="w-32 h-20 mx-auto mb-3 rounded-lg overflow-hidden border border-gray-200"><img src="../uploads/capas/<?php echo $capa; ?>" alt="Capa" class="w-full h-full object-cover"></div>
                                        <p class="text-xs text-gray-500 mb-2">Capa atual. Clique no botão abaixo para alterar.</p>
                                    <?php else: ?>
                                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-32 h-20 rounded-lg mx-auto mb-3 flex items-center justify-center"><span class="text-3xl">🌐</span></div>
                                        <p class="text-xs text-gray-500 mb-2">Sem capa cadastrada. Selecione uma imagem.</p>
                                    <?php endif; ?>
                                    
                                    <input type="file" name="capa" accept="image/*" class="hidden" id="input-capa" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                    <label for="input-capa" class="bg-white border border-gray-300 text-gray-600 text-xs font-semibold px-4 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition inline-block">Selecionar imagem</label>
                                    <p id="file-name" class="text-xs font-semibold text-senai-blue mt-2"></p>
                                    <p class="text-xs text-gray-400 mt-2">PNG, JPG ou WEBP. Máx. 2MB. Proporção recomendada: 16:9</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="form-label">Status do Curso</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="ativo" value="1" <?php echo($ativo == 1) ? 'checked' : ''; ?> class="accent-senai-green"><span class="text-sm text-gray-700">Ativo — Visível para os alunos</span></label>
                                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="ativo" value="0" <?php echo($ativo == 0) ? 'checked' : ''; ?> class="accent-gray-400"><span class="text-sm text-gray-500">Inativo — Oculto para os alunos</span></label>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-2 border-t border-gray-100"><button type="submit" class="bg-senai-blue hover:bg-senai-blue-dark text-white font-bold px-6 py-2.5 rounded-lg text-sm transition flex items-center gap-2">💾 Salvar Alterações</button><a href="cursos.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-lg text-sm transition flex items-center">Cancelar</a></div>

                        </form>
                    </div>
                </div>

                <div class="space-y-4">

                    <?php if (!empty($id)): ?>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <h3 class="font-bold text-gray-700 text-sm mb-3">Módulos deste Curso</h3>
                        <?php
    $sql_modulos = "SELECT m.*, (SELECT COUNT(*) FROM aulas a WHERE a.modulo_id = m.id) as total_aulas FROM modulos m WHERE curso_id = '$id' ORDER BY ordem ASC";
    $res_modulos = mysqli_query($conexao, $sql_modulos);
?>
                        <?php if (mysqli_num_rows($res_modulos) > 0): ?>
                            <ul class="space-y-2 text-sm max-h-48 overflow-y-auto">
                                <?php while ($mod = mysqli_fetch_assoc($res_modulos)): ?>
                                <li class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"><span class="text-gray-700 font-medium truncate pr-2"><?php echo $mod['ordem'] . '. ' . htmlspecialchars($mod['titulo']); ?></span><span class="text-xs text-gray-400 whitespace-nowrap"><?php echo $mod['total_aulas']; ?> aulas</span></li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-xs text-gray-500 text-center py-2 bg-gray-50 rounded-lg">Nenhum módulo cadastrado</p>
                        <?php endif; ?>
                        
                        <a href="modulos.php?curso_id=<?php echo $id; ?>" class="block mt-3 text-center border border-senai-blue text-senai-blue text-xs font-semibold py-2 rounded-lg hover:bg-blue-50 transition">Gerenciar Módulos</a>
                    </div>
                    <?php endif; ?>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="font-bold text-senai-blue text-sm mb-2">💡 Dicas</h4>
                        <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4"><li>Use títulos claros e atrativos</li><li>A capa deve ter boa resolução (min. 800×450px)</li><li>Cursos inativos não aparecem para alunos</li><li>Cadastre os módulos após criar o curso</li></ul>
                    </div>

                    <?php if (!empty($id)): ?>
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <h4 class="font-bold text-senai-red text-sm mb-2">⚠ Zona de Perigo</h4>
                        <p class="text-xs text-gray-600 mb-3">Excluir o curso também remove todos os módulos, aulas e inscrições vinculadas.</p>
                        <a href="curso_delete.php?id=<?php echo $id; ?>" onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.')" class="block w-full text-center bg-senai-red text-white text-xs font-bold py-2 rounded-lg hover:bg-red-700 transition">🗑 Excluir este curso</a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

<?php require_once "../includes/footer.php"; ?>
