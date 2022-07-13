
<?php
/*
    Livro: PHP: programando com Orientação a Objeto 
    Autor: Pablo Dall'Oglio
    Edição: 3ª 
    Capítulo de estudo: Cap. 01

*/

//============= include('arquivo.php'); =============
/**
 * inclui e avalia o arquivo informado
 * o codigo (variáveis, objetos e arrays) ficam disponíveis para serem usados pelo programa
 * os recursos do arquivo incluido fica disponível apartir da linha que o mesmo foi adicionado
 * caso o arquivo não seja encontrado é dado um erro warning (advertência)
 */



//============= require('arquivo.php'); =============
/**
 * Similar ao include, porém caso o arquivo não seja encontrado o erro gerado é fatal
 */



 //============= include_once('arquivo.php'); =============
/**
 * Similar ao include porém o arquivo só é chamado uma vez
 * isso evita duplicidade, redeclaração etc
 * caso o arquivo não seja encontrado o erro é waring
 */


 //============= require('arquivo.php'); =============
 /**
  * similar ao include_once, exceto pelo tipo erro
  * caso o arquivo não seja encontrado o erro é fatal
  */
?>