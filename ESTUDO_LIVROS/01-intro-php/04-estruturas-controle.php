
<?php
/*
    Livro: PHP: programando com Orientação a Objeto 
    Autor: Pablo Dall'Oglio
    Edição: 3ª 
    Capítulo de estudo: Cap. 01

*/
//============= IF =============
/*

if (expressao){
    comando se expressão for verdadeira;
} else {
    comando se não for verdadeira
}

*/



//============= SWITCH =============
/*

switch ($variável){
    case valor1:
        comandos;
        break;
    case valor2:
        comandos;
        break;
    default:
        comandos;
}

obs: se está com switch dentro de um loop e deseja continuar para a próxima iteração do laço de 
repetição, utilize o comando continue 2, que escapará dois níveis acima.
*/



//============= WHILE =============
/*

while (expressao){
    Comandos;
}

*/



//============= FOR =============
/*

for (expr1; expr2; expr3){
    Comandos;
}

expr1: Valor inicial da variável contadora
expr2: Condição de execução
expr3: valor a ser incrementado após a execução

exemplo:
for ($i; $i<=10; $i++){
    comandos;
}
*/



//============= FOREACH =============
/*

foreach ($array as $valor){
    instruções;
}

Ex:
$cores = array ("vermelho", "preto", "cinza", "roxo")
foreach($cores as $cor){
    print "$cor- ";
}
*/
$cores = array ("vermelho", "preto", "cinza", "roxo");
foreach($cores as $cor){
    print "$cor ";
}
echo ("<br>");



//============= CONTINUE =============
/*
    A instrução continue, quando executada em um bloco de comando FOR/WHILE, ignora as instruções 
    restantes até o fechamento em }.

*/



//============= BREAK =============
/*
    A instrução break aborta a execução de blocos de comandos (como if, while, for etc).
    
    Quando temos mais de um nível de iteração:
    while...
        for...
            break <qtd de níveis>

*/
?>