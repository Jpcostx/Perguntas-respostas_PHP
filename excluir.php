<?php
$id = $_GET['id'] ?? null;

if ($id) {
    $arq_perguntas = 'perguntas.txt';
    $arq_respostas = 'respostas.txt';

    if (file_exists($arq_perguntas)) {
        $linhas_p = file($arq_perguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $novas_p = array_filter($linhas_p, function($linha) use ($id) {
            $dados = explode(';', $linha);
            return ($dados[0] ?? '') !== $id;
        });
        file_put_contents($arq_perguntas, implode(PHP_EOL, $novas_p) . (empty($novas_p) ? '' : PHP_EOL));
    }

    if (file_exists($arq_respostas)) {
        $linhas_r = file($arq_respostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $novas_r = array_filter($linhas_r, function($linha) use ($id) {
            $dados = explode(';', $linha);
            return ($dados[1] ?? '') !== $id;
        });
        file_put_contents($arq_respostas, implode(PHP_EOL, $novas_r) . (empty($novas_r) ? '' : PHP_EOL));
    }
}

header("Location: index.php?sucesso=1");
exit;