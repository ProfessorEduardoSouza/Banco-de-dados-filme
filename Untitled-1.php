<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Filme e Busca de Filmes</title>
</head>
<body>
    <h1>Cadastro e Busca de Filmes</h1>

    
    <h2>Busca de Filmes</h2>
    <form method="GET">
        <label for="nome_filme">Digite o nome do filme:</label><br>
        <input type="text" id="nome_filme" name="nome_filme"><br>
        <input type="submit" value="Buscar">
    </form>

    <?php
    
    $api_key = 'a5c6899cb96227fad4b880bd52cf8b32';

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nome_filme'])) {
       
        $movie_name = urlencode($_GET['nome_filme']);

        
        $url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$movie_name&language=pt-BR";

        
        $ch = curl_init($url);

        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        
        $response = curl_exec($ch);

       
        curl_close($ch);

       
        $movie_data = json_decode($response, true);

        
        if ($movie_data && isset($movie_data['results']) && count($movie_data['results']) > 0) {
           
            $filme = $movie_data['results'][0];

           
            $titulo = $filme['title'];
            $data_lancamento = $filme["release_date"];
            $diretor = ''; 
            $sinopse = ''; 

            
            $filme_id = $filme['id'];

            
            $filme_url = "https://api.themoviedb.org/3/movie/$filme_id?api_key=$api_key&append_to_response=credits&language=pt-BR";

            
            $ch = curl_init($filme_url);

           
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $filme_info = curl_exec($ch);

           
            curl_close($ch);

           
            $filme_detalhes = json_decode($filme_info, true);

            
            if ($filme_detalhes) {
               
                foreach ($filme_detalhes['credits']['crew'] as $crew) {
                    if ($crew['job'] === "Director") {
                        $diretor = $crew['name'];
                        break;
                    }
                }
                $sinopse = $filme_detalhes['overview'];
            }

          
            echo "<h3>Resultado da Busca</h3>";
            echo "Titulo: $titulo<br>";
            echo "Data de lançamento: $data_lancamento<br>";
            echo "Diretor: $diretor<br>";
            echo "Sinopse: $sinopse<br>";

          
            echo "<h2>Cadastro de filme</h2>";
            echo '<form method="POST" action="">
                    <label for="filme">Nome do filme:</label>
                    <input type="text" name="filme" value="' . $titulo . '" required><br>
                    
                    <label for="dataFilme">Data:</label>
                    <input type="text" name="dataFilme" value="' . $data_lancamento . '" required><br>

                    <label for="diretorFilme">Diretor:</label>
                    <input type="text" name="diretorFilme" value="' . $diretor . '" required><br>

                    <label for="sinopseFilme">Sinopse:</label>
                    <input type="text" name="sinopseFilme" value="' . $sinopse . '" required><br>

                    <input type="submit" value="Cadastrar">
                </form>';
        } else {
            echo "Nenhum filme encontrado com o nome fornecido";
        }
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filme'])) {
        
        $nomeFilme = $_POST['filme'];
        $dataFilme = $_POST['dataFilme'];
        $diretorFilme = $_POST['diretorFilme'];
        $sinopseFilme = $_POST['sinopseFilme'];

        
        try {
           
            echo 'Filme cadastrado com sucesso!';
        } catch (PDOException $e) {
            echo 'Erro ao cadastrar filme: ' . $e->getMessage();
        }
    }
    ?>
</body>
</html>