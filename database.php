<?php
// Conectar com o bando de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "pesquisa";
$conn = mysqli_connect($servidor,$usuario,$senha,$banco);

if (!$conn)
{
    echo "deu ruim";
    echo "<br>";
}

?>


<?php
$msg = $_GET["msg"];
$cliente = $_GET["cliente"];

$resposta1 = "
    Olá {$cliente}, bem vindo!\n 
    Sou atendente virtual\n
    Gostaria de fazer a pesquisa? *[S/N]*
";

$resposta2 = "
    Pesquisa:\n
    Qual universo de super-heróis você prefere?\n
    *1 - DC COMICS*\n
    *2 - Marvel COMICS*\n
    Obs: Digite apenas o número da opção escolhida
";

$resposta3 = "
    Sua pesquisa foi realizada com sucesso!\n
    Muito obrigado!
";

$data = date("d-m-Y");

?>

<?php
$sql = "SELECT * FROM clientes WHERE Cliente = '$cliente'";
$query = mysqli_query($conn, $sql);
$total = mysqli_num_rows($query);

while ($rows_usuarios = mysqli_fetch_array($query)){
    $status = $rows_usuarios['Status'];
}

if ($total > 0){
    if ($status == 1){
        if (str_contains("SN", strtoupper($msg)[0])){
            if (strtoupper($msg)[0] == "N"){
                echo "Tudo bem, se quiser realizar a pesquisa é só nos chamar.";
                $novo_status = 1;
            }else{
                echo $resposta2;
                $novo_status = 2;
            }
        }else{
            echo $resposta1;
            $novo_status = 1;
        }
    }

    if ($status == 2){
        $voto = intval($msg[0]);
        if ($voto < 1){
            echo $resposta2;
            $novo_status = 2;
        }elseif ($voto > 2){
            echo $resposta2;
            $novo_status = 2;
        }else{
            echo $resposta3;
            $novo_status = 3;
        }
    }

    if ($status == 3){
        echo "Sua pesquisa já foi concluida";
        $novo_status = 3;
    }

}else{
    $status = 1;
    $sql = "INSERT INTO clientes (Cliente, `Status`) VALUES ('$cliente', '$status')";
    $query = mysqli_query($conn, $sql);
    if ($query){
        echo $resposta1;
        $novo_status = 1;
    }
}

?>

<?php
if ($status == 2){
    if ($novo_status == 3){
        $sql = "UPDATE clientes SET Voto = $voto WHERE Cliente = '$cliente'";
        $query = mysqli_query($conn, $sql);
    }

}

?>

<?php
$sql = "SELECT * FROM clientes WHERE Cliente = '$cliente'";
$query = mysqli_query($conn, $sql);
$total = mysqli_num_rows($query);

while ($rows_usuarios = mysqli_fetch_array($query)){
    $status = $rows_usuarios['Status'];
}

if ($status < 4){
    $sql = "UPDATE clientes SET `Status` = $novo_status WHERE Cliente = '$cliente'";
    $query = mysqli_query($conn, $sql);
}

?>
