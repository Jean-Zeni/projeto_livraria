<?php

include_once './config/config.php';
include_once './classes/Livro.php';
include_once './classes/Usuario.php';
include_once './classes/Autor.php';
include_once './classes/Editora.php';

session_start();

if (!isset($_SESSION['idUsu'])) {
    header('Location: index.php');
    exit();
}   

$autorid = new Autor($db);
$listaautor = $autorid->lerTodos();

$editoraId = new Editora($db);
$listaEditora = $editoraId->lerTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoLivro = new Livro($db);
    $nomeLivro = $_POST['nomeLivro'];
    $dataPubliLivro = $_POST['dataPubli'];
    $valorLivro = $_POST['valor'];
    $imgLivro = $_FILES['img'];
    $unidadesLivro = $_POST['unidades'];
    $fkIdAutor = $_POST['idAutor'];
    $fkIdEditora = $_POST['idEditora'];


    //Tratamento no upload da imagem

    //TAMANHO
    $nomeImg = "";
    if ($imgLivro['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($imgLivro['name'], PATHINFO_EXTENSION));
        $tamanhoMax = 10 * 1024 * 1024; // 10 MB

        if ($imgLivro['size'] > $tamanhoMax) {
            die("O arquivo não pode ser maior que 10MB.");
        }

        // Formatos permitidos
        $tiposPermitidos = ['jpg', 'jpeg', 'png'];
        if (!in_array($extensao, $tiposPermitidos)) {
            die("Apenas arquivos em formato PNG, JPG ou JPEG são aceitos.");
        }

        //GERA UM NOME ÚNICO PARA CADA IMAGEM
        $nomeImg = uniqid() . "." . $extensao;
        $destino = "../uploads/" . $nomeImg;

        //MOVE O ARQUIVO PARA A PASTA UPLOADS
        if (!move_uploaded_file($imgLivro['tmp_name'], $destino)) {
            die("Erro ao salvar imagem.");
        }
    } else if ($imgLivro['error'] !== UPLOAD_ERR_NO_FILE) {
        die("Erro ao fazer upload da imagem.");
    }

    $novoLivro->criar($nomeLivro, $dataPubliLivro, $valorLivro, $destino, $unidadesLivro, $fkIdAutor, $fkIdEditora);
    echo "Salvo com sucesso!";
    header('Location: cadLivro.php');
    exit();
}

// $novoUsuario->criar($nomeUsu, $sexoUsu, $foneUsu, $emailUsu, $senhaUsu);
// header('Location: login.php');
// exit();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Livro</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body id="telaCadLivro">

    <div id="formCadLivro">

        <h2>Cadastrar Novo Livro</h2>

        <form method="POST" id="cadLivro" enctype="multipart/form-data">
            <label for="nomeLivro">Nome do Livro</label><br>
            <input type="text" name="nomeLivro" class="inputNormal" id="nomeLivro"><br><br>

            <label for="dataPubli">Data de Publicação</label><br>
            <input type="date" name="dataPubli" class="inputNormal" id="dataPubli"><br><br>

            <label for="valor">Valor do Livro:</label><br>
            <input type="number" name="valor" class="inputsNumber" step="0.01"><br><br>

            <label for="idAutor">Autor:</label><br>
            <select name="idAutor" require>
                <option value="">selecione o autor</option>
                <?php foreach ($listaautor as $listaautores): ?>
                    <option value="<?php echo $listaautores['pk_id_autor']; ?>">
                        <?php echo $listaautores['nome_autor']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <label for="idEditora">Editora:</label><br>
            <select name="idEditora" require>
                <option value="">selecione a editora</option>
                <?php foreach ($listaEditora as $listaEditoras): ?>
                    <option value="<?php echo $listaEditoras['pk_id_editora']; ?>">
                        <?php echo $listaEditoras['nome_editora']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <input type="file" class="inputSelectImg" name="img" accept=".jpg, .png, .jpeg"><br>        
            <br>

            <label for="unidades">Número de Livros:</label><br>
            <input type="number" name="unidades" class="inputsNumber" step="1"><br><br>

            <input type="submit" class="btnNormal" value="Adicionar"><br><br>
            <input type="reset" class="btnNormal" value="Limpar"><br><br>
        </form>

        <button class="btnNormal" onclick="location.href='home.php'">Voltar</button>
    </div>
</body>

</html>