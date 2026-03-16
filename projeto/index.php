<?php
// ============================================
// Arquivo: index.php
// Função: Página inicial pública da plataforma
// ============================================

require_once "conexao.php";

// Buscar os 3 cursos mais recentes para exibir como destaque
$sql_cursos = "SELECT * FROM cursos WHERE ativo = 1 ORDER BY id DESC LIMIT 3";
$res_cursos = mysqli_query($conexao, $sql_cursos);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAD SENAI — Plataforma de Ensino</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { senai: { red:'#C0392B', blue:'#34679A', 'blue-dark':'#2C5A85', orange:'#E67E22', green:'#27AE60' } } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <nav class="bg-senai-blue shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-white font-extrabold text-lg">🎓 <span>EAD SENAI</span></a>
            <div class="flex items-center gap-6 text-sm">
                <a href="#cursos" class="text-blue-200 hover:text-white transition">Cursos</a>
                <a href="#sobre" class="text-blue-200 hover:text-white transition">Sobre</a>
                <a href="login.php" class="border border-white text-white px-4 py-1.5 rounded hover:bg-white hover:text-senai-blue transition font-semibold">Entrar</a>
                <a href="cadastro.php" class="bg-senai-green text-white px-4 py-1.5 rounded hover:bg-green-600 transition font-semibold">Cadastrar-se</a>
            </div>
        </div>
    </nav>

    <section class="bg-gradient-to-br from-senai-blue-dark via-senai-blue to-blue-500 text-white py-24 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-5">Aprenda no seu ritmo, de qualquer lugar.</h1>
            <p class="text-lg text-blue-100 mb-8 max-w-xl mx-auto">Acesse cursos completos, com módulos organizados e aulas em vídeo.</p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="cadastro.php" class="bg-yellow-400 text-gray-900 font-bold px-8 py-3 rounded-lg text-sm hover:bg-yellow-300 transition shadow-lg">Quero me Cadastrar — É Grátis!</a>
                <a href="#cursos" class="border-2 border-white/50 text-white font-semibold px-8 py-3 rounded-lg text-sm hover:bg-white/10 transition">Ver Cursos Disponíveis</a>
            </div>
        </div>
    </section>

    <section id="cursos" class="py-16 px-6 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Cursos em Destaque</h2>
                <p class="text-gray-500">Comece sua jornada de aprendizado hoje mesmo.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while($curso = mysqli_fetch_assoc($res_cursos)): ?>
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-bold text-gray-800 text-base mb-2"><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        <p class="text-sm text-gray-500 mb-4 flex-1"><?php echo htmlspecialchars($curso['descricao']); ?></p>
                        <a href="cadastro.php" class="bg-senai-blue text-white text-sm font-semibold py-2 rounded-lg text-center hover:bg-senai-blue-dark transition">Inscrever-se Grátis</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 text-sm">
        <p>© 2024 SENAI. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
