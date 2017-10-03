<?php

use MotaMonteiro\Loteria\MegaSena;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$loteria = isset($_SESSION['AppLoteria']) ? unserialize($_SESSION['AppLoteria']) : new MegaSena();
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';
$aposta = isset($_POST['aposta']) ? $_POST['aposta'] : '';

if ($acao == 'adicionarAposta') {

    if (!empty($loteria->getSorteio())) {
        $estilo = 'danger';
        $msg = 'Não é possível adicionar apostas após a geração do sorteio.';
    } else {
        $numeros = explode(',', $aposta);

        if (!is_array($numeros) || count($numeros) != $loteria->getQtdNumeroAposta()) {
            $estilo = 'danger';
            $msg = 'Erro ao adicionar aposta (' . $aposta . '). Por favor preencha '.$loteria->getQtdNumeroAposta().' números separados por vírgula.';
        } else {

            $loteria->adicionarAposta($numeros);
            $_SESSION['AppLoteria'] = serialize($loteria);
            $estilo = 'success';
            $msg = 'Aposta ' . $aposta . ' adicionada com sucesso';
        }
    }
}

if ($acao == 'adicionarApostaSurpresinha') {

    if (!empty($loteria->getSorteio())) {
        $estilo = 'danger';
        $msg = 'Não é possível adicionar aposta surpresinha após a geração do sorteio.';
    } else {
        $numeros =  $loteria->gerarApostaSurpresinha();
        $loteria->adicionarAposta($numeros);
        $_SESSION['AppLoteria'] = serialize($loteria);
        $estilo = 'success';
        $msg = 'Aposta surpresinha ' . implode(',', $numeros) . ' adicionada com sucesso';
    }
}

if ($acao == 'gerarSoirteio') {
    $loteria->simularSorteio();
    $loteria->consultarAcertos();
    $_SESSION['AppLoteria'] = serialize($loteria);
    $estilo = 'success';
    $msg = 'Sorteio realizado com sucesso ' . implode(',', $loteria->getSorteio());
}

if ($acao == 'reiniciar') {
    unset($_SESSION['AppLoteria']);
    $loteria = new MegaSena();
    $estilo = 'success';
    $msg = 'Aplicação reiniciada com sucesso.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Loteria</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"
          integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
<h1>Loteria</h1>
<h3>Cadastro de apostas</h3>

<?php if ($acao != '') { ?>
    <div class="alert alert-<?php echo $estilo; ?>" role="alert">
        <?php echo $msg; ?>
    </div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="acao" id="acao" value="">
    <div class="form-group">
        <label class="form-control-label" for="apostas">Insira aqui <?php echo $loteria->getQtdNumeroAposta(); ?> números separados por vírgula.</label>
        <input type="text" class="form-control" name="aposta" id="aposta" placeholder="<?php echo $loteria->getQtdNumeroAposta(); ?> números separados por vírgula">
    </div>
    <button type="submit" class="btn btn-primary" id="botaoAposta">Adicionar aposta</button>
    <button type="submit" class="btn btn-primary" id="botaoSurpersinha">Adicionar surpresinha</button>
    <button type="submit" class="btn btn-primary" id="botaoGerarSorteio">Gerar Sorteio</button>
    <button type="submit" class="btn btn-primary" id="botaoReiniciar">Reiniciar aplicação</button>
</form>
<br>
<h3>Minhas Apostas</h3>
<p>
    <?php foreach ($loteria->getApostas() as $minhaAposta) {
        echo 'Identificador: ' . $minhaAposta['identificador'] . ' Números: ' . implode(',', $minhaAposta['numeros']) . ' Data da Aposta: ' . $minhaAposta['dataAposta'];
        if (!empty($loteria->getSorteio())) {
            echo ' <b>(' . $minhaAposta['acertos'] . ' acertos)</b>';
        }
        echo '<br>';
    } ?>
</p>

<?php if (!empty($loteria->getSorteio())) { ?>
    <h3>Resultado da Mega Sena</h3>
    <p>
        Resultado: <?php echo implode(',', $loteria->getSorteio()); ?>
    </p>
<?php } ?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
        integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"
        integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"
        crossorigin="anonymous"></script>
<script>
    $('#botaoAposta').click(function () {
        $('#acao').val('adicionarAposta');
    });

    $('#botaoSurpersinha').click(function () {
        $('#acao').val('adicionarApostaSurpresinha');
    });

    $('#botaoGerarSorteio').click(function () {
        $('#acao').val('gerarSoirteio');
    });

    $('#botaoReiniciar').click(function () {
        $('#acao').val('reiniciar');
    });

</script>
</body>
</html>