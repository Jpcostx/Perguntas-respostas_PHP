<?php
$id_pergunta = $_GET['id'] ?? null;

if (!$id_pergunta) {
    header("Location: index.php");
    exit;
}

$arq_perguntas = 'perguntas.txt';
$arq_respostas = 'respostas.txt';

$linhas_p = file($arq_perguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$linhas_r = file($arq_respostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$pergunta_encontrada = null;
foreach ($linhas_p as $l) {
    $d = explode(';', $l);
    if ($d[0] === $id_pergunta) {
        $pergunta_encontrada = ['id' => $d[0], 'texto' => $d[1]];
        break;
    }
}

if (!$pergunta_encontrada) {
    die("Pergunta não encontrada.");
}

$respostas_encontradas = [];
foreach ($linhas_r as $l) {
    $d = explode(';', $l);
    if (($d[1] ?? '') === $id_pergunta) {
        $respostas_encontradas[] = [
            'id' => $d[0],
            'texto' => $d[2],
            'correta' => $d[3] ?? '0'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Pergunta #<?= htmlspecialchars($pergunta_encontrada['id']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container form-container">
        <h2>Pergunta #<?= htmlspecialchars($pergunta_encontrada['id']) ?></h2>
        
        <p class="pergunta-destaque"><?= htmlspecialchars($pergunta_encontrada['texto']) ?></p>

        <h3>Opções de Resposta:</h3>
        <ul class="lista-respostas">
            <?php foreach ($respostas_encontradas as $r): ?>
                <li class="<?= $r['correta'] == '1' ? 'resposta-correta' : '' ?>">
                    <?= htmlspecialchars($r['texto']) ?>
                    <?php if ($r['correta'] == '1'): ?>
                        <span class="tag-correta">✓ Correta</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="acoes">
            <a href="index.php" class="btn btn-voltar">Voltar</a>
            <a href="editar.php?id=<?= urlencode($pergunta_encontrada['id']) ?>" class="btn btn-salvar">Editar</a>
        </div>
    </div>
</body>
</html>