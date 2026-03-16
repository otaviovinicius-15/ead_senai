<?php
// ============================================
// Arquivo: admin/modulo_form.php
// Função: Formulário para cadastrar ou editar módulos
// ============================================

session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao.php";

$id = "";
$curso_id = "";
$titulo = "";
$descricao = "";
$ordem = 1;
$acao = "Adicionar Novo Módulo";

if (isset($_GET['curso_id'])) {
    $curso_id = $_GET['curso_id'];
    $sql_ordem = "SELECT MAX(ordem) as max_ordem FROM modulos WHERE curso_id = '$curso_id'";
    $res_ordem = mysqli_query($conexao, $sql_ordem);
    if ($row = mysqli_fetch_assoc($res_ordem)) {
        $ordem = (int)$row['max_ordem'] + 1;
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_busca = "SELECT * FROM modulos WHERE id = '$id'";
    $resultado = mysqli_query($conexao, $sql_busca);

    if ($modulo = mysqli_fetch_assoc($resultado)) {
        $curso_id = $modulo['curso_id'];
        $titulo = $modulo['titulo'];
        $descricao = $modulo['descricao'];
        $ordem = $modulo['ordem'];
        $acao = "Editar Módulo";
    }
}

$sql_cursos = "SELECT id, titulo FROM cursos ORDER BY titulo ASC";
$res_cursos = mysqli_query($conexao, $sql_cursos);

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_post = $_POST['id'];
    $curso_id_post = $_POST['curso_id'];
    $titulo_post = $_POST['titulo'];
    $descricao_post = $_POST['descricao'];
    $ordem_post = $_POST['ordem'];

    if (empty($curso_id_post) || empty($titulo_post)) {
        $erro = "Selecione o curso e preencha o título.";
    } else {
        if (!empty($id_post)) {
            $sql = "UPDATE modulos SET curso_id = '$curso_id_post', titulo = '$titulo_post', descricao = '$descricao_post', ordem = '$ordem_post' WHERE id = '$id_post'";
        } else {
            $sql = "INSERT INTO modulos (curso_id, titulo, descricao, ordem) VALUES ('$curso_id_post', '$titulo_post', '$descricao_post', '$ordem_post')";
        }

        if (mysqli_query($conexao, $sql)) {
            header("Location: modulos.php?curso_id=$curso_id_post&msg=salvo");
            exit;
        } else {
            $erro = "Erro ao salvar no banco de dados: " . mysqli_error($conexao);
        }
    }
}

require_once "../includes/header.php";
?>
<title><?php echo $acao; ?> — Admin | EAD SENAI</title>

<body class="bg-gray-100 min-h-screen flex">

    <?php require_once "includes/menu_admin.php"; ?>

    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                <?php if (!empty($curso_id)): ?>
                <a href="modulos.php?curso_id=<?php echo $curso_id; ?>" class="hover:text-senai-blue">Módulos</a> ›
                <?php endif; ?>
                <span class="text-gray-700 font-semibold"><?php echo $acao; ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?php echo $acao; ?></h1>
        </div>

        <div class="p-6 flex-1 max-w-xl">
            
            <?php if (!empty($erro)): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="text-red-500 font-bold text-base">⚠</span><span><?php echo $erro; ?></span></div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="modulo_form.php" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    
                    <div class="mb-4">
                        <label class="form-label">Curso *</label>
                        <select name="curso_id" class="form-input" required>
                            <option value="">-- Selecione o Curso --</option>
                            <?php while ($curso = mysqli_fetch_assoc($res_cursos)): ?>
                                <option value="<?php echo $curso['id']; ?>" <?php echo($curso_id == $curso['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso['titulo']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Título do Módulo *</label>
                        <input type="text" name="titulo" class="form-input" placeholder="Ex: Introdução ao HTML" value="<?php echo htmlspecialchars($titulo); ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Descrição (opcional)</label>
                        <textarea name="descricao" rows="3" class="form-input resize-none" placeholder="Fundamentos da linguagem..."><?php echo htmlspecialchars($descricao); ?></textarea>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-input" value="<?php echo $ordem; ?>" min="1" required>
                    </div>
                    
                    <div class="flex gap-2"><button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button><a href="modulos.php?curso_id=<?php echo $curso_id; ?>" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a></div>
                </form>
            </div>
        </div>
    </main>

<?php require_once "../includes/footer.php"; ?>
