<?php
// ============================================
// Arquivo: admin/aula_form.php
// Função: Formulário para cadastrar ou editar aulas
// ============================================

session_start();

// Verifica se está logado e se é admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$id = "";
$modulo_id = "";
$curso_id = "";
$titulo = "";
$video_url = "";
$duracao = "";
$descricao = "";
$ordem = 1;
$acao = "Adicionar Nova Aula";

// Recebe do GET se vier (para novo cadastro e navegação)
if (isset($_GET['curso_id']))
    $curso_id = $_GET['curso_id'];
if (isset($_GET['modulo_id'])) {
    $modulo_id = $_GET['modulo_id'];

    // Calcula proxima ordem caso seja novo cadastro
    $sql_ordem = "SELECT MAX(ordem) as max_ordem FROM aulas WHERE modulo_id = '$modulo_id'";
    $res_ordem = mysqli_query($conexao, $sql_ordem);
    if ($row = mysqli_fetch_assoc($res_ordem)) {
        $ordem = (int)$row['max_ordem'] + 1;
    }
}

// Verifica se foi passado um ID de aula na URL (modo de edição)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_busca = "SELECT * FROM aulas WHERE id = '$id'";
    $resultado = mysqli_query($conexao, $sql_busca);

    if ($aula = mysqli_fetch_assoc($resultado)) {
        $modulo_id = $aula['modulo_id'];
        $titulo = $aula['titulo'];
        $video_url = $aula['video_url'];
        $duracao = $aula['duracao'];
        $descricao = $aula['descricao'];
        $ordem = $aula['ordem'];
        $acao = "Editar Aula";

        // Seedição direta e curso_id não veio no GET, buscamos pelo modulo
        if (empty($curso_id)) {
            $sql_curso_busca = "SELECT curso_id FROM modulos WHERE id = '$modulo_id'";
            $res_cb = mysqli_query($conexao, $sql_curso_busca);
            if ($row_cb = mysqli_fetch_assoc($res_cb)) {
                $curso_id = $row_cb['curso_id'];
            }
        }
    }
}

// Obter a lista de módulos (filtrados pelo curso_id se existir para facilitar a seleção)
$sql_modulos = "SELECT m.id, m.titulo as modulo_titulo, c.titulo as curso_titulo 
                FROM modulos m 
                INNER JOIN cursos c ON m.curso_id = c.id ";
if (!empty($curso_id)) {
    $sql_modulos .= " WHERE m.curso_id = '$curso_id' ";
}
$sql_modulos .= " ORDER BY c.titulo ASC, m.ordem ASC";
$res_modulos = mysqli_query($conexao, $sql_modulos);

// Processar o formulário quando for enviado via POST
$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_post = $_POST['id'];
    $modulo_id_post = $_POST['modulo_id'];
    $curso_id_post = $_POST['curso_id']; // Usado para redirecionamento
    $titulo_post = $_POST['titulo'];
    $video_url_post = $_POST['video_url'];
    $duracao_post = $_POST['duracao'];
    $descricao_post = $_POST['descricao'];
    $ordem_post = $_POST['ordem'];

    if (empty($modulo_id_post) || empty($titulo_post)) {
        $erro = "Selecione o módulo e preencha o título.";
    }
    else {
        if (!empty($id_post)) {
            // Modo Edição: UPDATE
            $sql = "UPDATE aulas SET 
                    modulo_id = '$modulo_id_post',
                    titulo = '$titulo_post', 
                    video_url = '$video_url_post',
                    duracao = '$duracao_post',
                    descricao = '$descricao_post', 
                    ordem = '$ordem_post'
                    WHERE id = '$id_post'";
        }
        else {
            // Modo Cadastro: INSERT
            $sql = "INSERT INTO aulas (modulo_id, titulo, video_url, duracao, descricao, ordem) 
                    VALUES ('$modulo_id_post', '$titulo_post', '$video_url_post', '$duracao_post', '$descricao_post', '$ordem_post')";
        }

        if (mysqli_query($conexao, $sql)) {
            header("Location: aulas.php?modulo_id=$modulo_id_post&curso_id=$curso_id_post&msg=salvo");
            exit;
        }
        else {
            $erro = "Erro ao salvar no banco de dados: " . mysqli_error($conexao);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $acao; ?> — Admin | EAD SENAI</title>
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
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                <?php if (!empty($curso_id)): ?>
                <a href="modulos.php?curso_id=<?php echo $curso_id; ?>" class="hover:text-senai-blue">Módulos</a> ›
                <a href="aulas.php?modulo_id=<?php echo $modulo_id; ?>&curso_id=<?php echo $curso_id; ?>" class="hover:text-senai-blue">Aulas</a> ›
                <?php
endif; ?>
                <span class="text-gray-700 font-semibold"><?php echo $acao; ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?php echo $acao; ?></h1>
        </div>

        <div class="p-6 flex-1 max-w-xl">
            
            <?php if (!empty($erro)): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="text-red-500 font-bold text-base">⚠</span>
                <span><?php echo $erro; ?></span>
            </div>
            <?php
endif; ?>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="aula_form.php" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($curso_id); ?>">
                    
                    <div class="mb-4">
                        <label class="form-label">Módulo *</label>
                        <select name="modulo_id" class="form-input" required>
                            <option value="">-- Selecione o Módulo --</option>
                            <?php while ($mod = mysqli_fetch_assoc($res_modulos)): ?>
                                <option value="<?php echo $mod['id']; ?>" <?php echo($modulo_id == $mod['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mod['curso_titulo'] . ' - ' . $mod['modulo_titulo']); ?>
                                </option>
                            <?php
endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Título da Aula *</label>
                        <input type="text" name="titulo" class="form-input" placeholder="Ex: Tags Essenciais do HTML" value="<?php echo htmlspecialchars($titulo); ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">URL do Vídeo (embed)</label>
                        <input type="url" name="video_url" class="form-input" value="<?php echo htmlspecialchars($video_url); ?>" placeholder="https://www.youtube.com/embed/...">
                        <p class="text-xs text-gray-400 mt-1">Use a URL de incorporação do YouTube ou Vimeo.</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Duração</label>
                        <input type="text" name="duracao" class="form-input" value="<?php echo htmlspecialchars($duracao); ?>" placeholder="Ex: 15:10">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Descrição (opcional)</label>
                        <textarea name="descricao" rows="4" class="form-input resize-none" placeholder="Conteúdo da aula..."><?php echo htmlspecialchars($descricao); ?></textarea>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-input" value="<?php echo $ordem; ?>" min="1" required>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar Aula</button>
                        <a href="aulas.php?modulo_id=<?php echo $modulo_id; ?>&curso_id=<?php echo $curso_id; ?>" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
