<?php
$arq_perguntas = 'perguntas.txt';
$arq_respostas = 'respostas.txt';

if (!file_exists($arq_perguntas)) touch($arq_perguntas);
if (!file_exists($arq_respostas)) touch($arq_respostas);

$linhas_perguntas = file($arq_perguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$linhas_respostas = file($arq_respostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$contagem_respostas = [];
foreach ($linhas_respostas as $linha) {
    $dados = explode(';', $linha);
    $id_p = $dados[1] ?? '';
    if ($id_p) {
        $contagem_respostas[$id_p] = ($contagem_respostas[$id_p] ?? 0) + 1;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Perguntas - Game Corporativo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-action">
            <h2>Perguntas Cadastradas</h2>
            <a href="cadastrar.php" class="btn btn-salvar">+ Nova Pergunta</a>
        </div>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alerta sucesso">Operação realizada com sucesso!</div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pergunta</th>
                    <th>Opções</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($linhas_perguntas as $linha): ?>
                    <?php 
                        $dados = explode(';', $linha);
                        $id = $dados[0] ?? '';
                        $texto = $dados[1] ?? '';
                        $total_r = $contagem_respostas[$id] ?? 0;
                    ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($id) ?></strong></td>
                        <td><?= htmlspecialchars($texto) ?></td>
                        <td><?= $total_r ?> opções</td>
                        <td class="acoes-tabela">
                            <a href="visualizar.php?id=<?= urlencode($id) ?>" class="btn btn-sm">Ver</a>
                            <a href="editar.php?id=<?= urlencode($id) ?>" class="btn btn-sm btn-editar">Editar</a>
                            <a href="excluir.php?id=<?= urlencode($id) ?>" class="btn btn-sm btn-erro" onclick="return confirm('Excluir esta pergunta e todas as suas respostas?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($linhas_perguntas)): ?>
                    <tr><td colspan="4">Nenhuma pergunta cadastrada até o momento.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>