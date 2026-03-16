<?php
// ============================================
// Arquivo: login.php
// Função: Tela de login e autenticação do usuário
// ============================================

session_start();

if (isset($_SESSION["usuario_id"])) {
    if ($_SESSION["usuario_tipo"] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: meus_cursos.php");
    }
    exit;
}

require_once "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexao, $sql);

    if ($usuario = mysqli_fetch_assoc($resultado)) {
        if (password_verify($senha, $usuario["senha"])) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            $_SESSION["usuario_email"] = $usuario["email"];
            $_SESSION["usuario_tipo"] = $usuario["tipo"];

            if ($usuario["tipo"] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: meus_cursos.php");
            }
            exit;
        } else {
            $erro = "E-mail ou senha incorretos. Tente novamente.";
        }
    } else {
        $erro = "E-mail ou senha incorretos. Tente novamente.";
    }
}

require_once "includes/header.php";
?>
<title>Login — EAD SENAI</title>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <nav class="bg-senai-blue shadow-md">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-white font-extrabold text-lg">🎓 <span>EAD SENAI</span></a>
            <a href="cadastro.php" class="text-blue-200 hover:text-white text-sm transition">Não tem conta? <span class="underline font-semibold">Cadastre-se</span></a>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-senai-blue px-8 py-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3"><span class="text-3xl">🔐</span></div>
                    <h1 class="text-white font-extrabold text-xl">Entrar na Plataforma</h1>
                    <p class="text-blue-200 text-sm mt-1">Informe suas credenciais para acessar</p>
                </div>
                <div class="px-8 py-6">
                    <?php if (!empty($erro)): ?>
                    <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm"><span class="text-red-500 font-bold text-base">⚠</span><span><?php echo $erro; ?></span></div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">E-mail</label>
                            <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">✉</span><input type="email" name="email" required placeholder="seu@email.com" class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-senai-blue focus:border-transparent"></div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Senha</label>
                            <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔒</span><input type="password" name="senha" required placeholder="••••••••" class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-senai-blue focus:border-transparent"></div>
                        </div>
                        <button type="submit" class="w-full bg-senai-blue hover:bg-senai-blue-dark text-white font-bold py-3 rounded-lg transition text-sm">Entrar na Plataforma</button>
                    </form>
                    <div class="relative my-5"><div class="border-t border-gray-200"></div><span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-3 text-xs text-gray-400">ou</span></div>
                    <a href="cadastro.php" class="block w-full border-2 border-senai-blue text-senai-blue font-bold py-2.5 rounded-lg text-sm text-center hover:bg-blue-50 transition">Criar nova conta</a>
                </div>
            </div>
            <div class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-3 text-xs text-gray-600 text-center">
                <strong>Admin?</strong> Use <span class="font-mono bg-white px-1 rounded">admin@ead.com</span> / <span class="font-mono bg-white px-1 rounded">admin123</span> → <a href="admin/index.php" class="text-senai-blue underline font-semibold">Painel Admin</a>
            </div>
            <p class="text-center text-xs text-gray-400 mt-5"><a href="index.php" class="hover:text-senai-blue transition">← Voltar à página inicial</a></p>
        </div>
    </main>

    <footer class="bg-senai-blue text-blue-200 text-center text-xs py-3">
        SENAI — Sistema EAD &nbsp;|&nbsp; Todos os direitos reservados
    </footer>

<?php require_once "includes/footer.php"; ?>
