<?php
$arq_perguntas = 'perguntas.txt';
$arq_respostas = 'respostas.txt';
$erro = '';

$id_pergunta = $_REQUEST['id'] ?? null;

if (!$id_pergunta) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pergunta = trim($_POST['pergunta']);
    $respostas = $_POST['respostas'] ?? [];
    $correta = $_POST['correta'] ?? null;

    if (empty($pergunta)) {
        $erro = "A pergunta é obrigatória.";
    } elseif ($correta === null) {
        $erro = "Selecione a resposta correta.";
    } else {
        $linhas_p = file($arq_perguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $novas_p = [];
        foreach ($linhas_p as $l) {
            $d = explode(';', $l);
            if ($d[0] === $id_pergunta) {
                $pergunta_limpa = str_replace(';', '', $pergunta);
                $novas_p[] = "$id_pergunta;$pergunta_limpa";
            } else {
                $novas_p[] = $l;
            }
        }
        file_put_contents($arq_perguntas, implode(PHP_EOL, $novas_p) . PHP_EOL);

        $linhas_r = file($arq_respostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $novas_r = [];
        
        foreach ($linhas_r as $l) {
            $d = explode(';', $l);
            if (($d[1] ?? '') !== $id_pergunta) {
                $novas_r[] = $l;
            }
        }

        $ultimo_id_r = 0;
        foreach ($novas_r as $l) {
            $d = explode(';', $l);
            if ((int)$d[0] > $ultimo_id_r) $ultimo_id_r = (int)$d[0];
        }

        foreach ($respostas as $index => $texto_r) {
            $texto_r = trim($texto_r);
            if (!empty($texto_r)) {
                $ultimo_id_r++;
                $is_correta = ($correta == $index) ? 1 : 0;
                $texto_r_limpo = str_replace(';', '', $texto_r);
                $novas_r[] = "$ultimo_id_r;$id_pergunta;$texto_r_limpo;$is_correta";
            }
        }

        file_put_contents($arq_respostas, implode(PHP_EOL, $novas_r) . PHP_EOL);

        header("Location: index.php?sucesso=1");
        exit;
    }
}

$linhas_p = file($arq_perguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$pergunta_dados = null;
foreach ($linhas_p as $l) {
    $d = explode(';', $l);
    if ($d[0] === $id_pergunta) {
        $pergunta_dados = ['texto' => $d[1]];
        break;
    }
}

$linhas_r = file($arq_respostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$respostas_dados = [];
foreach ($linhas_r as $l) {
    $d = explode(';', $l);
    if (($d[1] ?? '') === $id_pergunta) {
        $respostas_dados[] = ['texto' => $d[2], 'correta' => $d[3] ?? '0'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pergunta #<?= htmlspecialchars($id_pergunta) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container form-container">
        <h2>Editar Pergunta #<?= htmlspecialchars($id_pergunta) ?></h2>

        <?php if ($erro): ?>
            <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form action="editar.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id_pergunta) ?>">

            <div class="form-group">
                <label>Texto da Pergunta</label>
                <textarea name="pergunta" rows="3" required><?= htmlspecialchars($pergunta_dados['texto'] ?? '') ?></textarea>
            </div>

            <h3>Respostas de Múltipla Escolha</h3>

            <?php for ($i = 0; $i < 4; $i++): ?>
                <?php 
                    $val_texto = $respostas_dados[$i]['texto'] ?? '';
                    $val_correta = ($respostas_dados[$i]['correta'] ?? '0') == '1';
                ?>
                <div class="form-group-opcao">
                    <input type="radio" name="correta" value="<?= $i ?>" <?= $val_correta ? 'checked' : '' ?>>
                    <input type="text" name="respostas[<?= $i ?>]" value="<?= htmlspecialchars($val_texto) ?>" placeholder="Opção <?= $i + 1 ?>">
                </div>
            <?php endfor; ?>

            <div class="acoes">
                <a href="index.php" class="btn btn-voltar">Voltar</a>
                <button type="submit" class="btn btn-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</body>
</html>