
<?php
/*
    Livro: PHP: programando com Orientação a Objeto 
    Autor: Pablo Dall'Oglio
    Edição: 3ª 
    Capítulo de estudo: Cap. 01

*/
    // echo imprime na tela ou no console
    echo 'Hello World!'.'<br>'; //<br> é uma tag html para quebra de linha. 

    // print é um comando que imprime uma string no console
    print 'Imprimindo String'.'<br>';

    // var_dump é uma funçãi que mostra no console ou página o conteúdo de variáveis. 
    $vetor = array ('Flamengo', 'O mais querido', 'do Brasil!'); // declarando um array de exemplo
    var_dump($vetor); //lendo o array com var_dump
    echo '<br>'; //pulando linha para a saída n ficar misturada no próx. exemplo

    // print_r funciona parecido como var_dump. Porém a saída é mais limpa para ler
    print_r($vetor);

?>