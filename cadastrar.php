<?php
$arq_perguntas = 'perguntas.txt';
$arq_respostas = 'respostas.txt';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pergunta = trim($_POST['pergunta']);
    $respostas = $_POST['respostas'] ?? [];
    $correta = $_POST['correta'] ?? null;

    if (empty($pergunta)) {
        $erro = "O texto da pergunta é obrigatório.";
    } elseif (count(array_filter($respostas)) < 2) {
        $erro = "Cadastre pelo menos 2 alternativas de resposta.";
    } elseif ($correta === null) {
        $erro = "Selecione qual das alternativas é a resposta correta.";
    } else {
        $linhas_p = file_exists($arq_perguntas) ? file($arq_perguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $ultimo_id_p = 0;
        foreach ($linhas_p as $l) {
            $d = explode(';', $l);
            if ((int)$d[0] > $ultimo_id_p) $ultimo_id_p = (int)$d[0];
        }
        $novo_id_p = $ultimo_id_p + 1;

        $pergunta_limpa = str_replace(';', '', $pergunta);
        file_put_contents($arq_perguntas, "$novo_id_p;$pergunta_limpa" . PHP_EOL, FILE_APPEND);

        $linhas_r = file_exists($arq_respostas) ? file($arq_respostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $ultimo_id_r = 0;
        foreach ($linhas_r as $l) {
            $d = explode(';', $l);
            if ((int)$d[0] > $ultimo_id_r) $ultimo_id_r = (int)$d[0];
        }

        foreach ($respostas as $index => $texto_r) {
            $texto_r = trim($texto_r);
            if (!empty($texto_r)) {
                $ultimo_id_r++;
                $is_correta = ($correta == $index) ? 1 : 0;
                $texto_r_limpo = str_replace(';', '', $texto_r);
                file_put_contents($arq_respostas, "$ultimo_id_r;$novo_id_p;$texto_r_limpo;$is_correta" . PHP_EOL, FILE_APPEND);
            }
        }

        header("Location: index.php?sucesso=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Pergunta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container form-container">
        <h2>Criar Nova Pergunta</h2>

        <?php if ($erro): ?>
            <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form action="cadastrar.php" method="POST">
            <div class="form-group">
                <label>Texto da Pergunta</label>
                <textarea name="pergunta" rows="3" placeholder="Digite a pergunta..." required></textarea>
            </div>

            <h3>Respostas de Múltipla Escolha</h3>
            <p class="subtexto">Preencha as alternativas e marque o botão referente à resposta correta.</p>

            <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="form-group-opcao">
                    <input type="radio" name="correta" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>>
                    <input type="text" name="respostas[<?= $i ?>]" placeholder="Opção <?= $i + 1 ?>" <?= $i < 2 ? 'required' : '' ?>>
                </div>
            <?php endfor; ?>

            <div class="acoes">
                <a href="index.php" class="btn btn-voltar">Voltar</a>
                <button type="submit" class="btn btn-salvar">Salvar Pergunta</button>
            </div>
        </form>
    </div>
</body>
</html>