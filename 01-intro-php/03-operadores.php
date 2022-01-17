
<?php
/*
    Livro: PHP: programando com Orientação a Objeto 
    Autor: Pablo Dall'Oglio
    Edição: 3ª 
    Capítulo de estudo: Cap. 01

*/
//============= Atribuição (=) =============
$num = 100; // variavel num recebe 100
echo ("$num <br>");


//============= Aritméticos =============
/* 
    + | Soma
    - | Subtração
    * | Multiplicação
    / | Divisão
    % | Resto
*/


//============= Atribuição + aritmétricos =============
$num +=15; // soma 100 com 15
$num -=5; // subtrai 5 de 115
$num *=2; // multiplica 110 por 2
$num /=4; // divide 220 por 4
echo ("$num <br>"); // exibe 55 


//============= incremento e decremento =============
/*
    ++$num | Pré-incremento. (incrementa $num em 1 e então retorna $num)
    $num++ | Pós-incremento. (retorna $num e então incrementa $num em 1)
    ++$num | Pré-decremento. (decrementa $num em 1 e então retorna $num)
    $num++ | Pós-decremento. (retorna $num e então decrementa $num em 1)

*/
++$num; // Pré-incremento
echo ("$num<br>"); // output de $num incrementada

++$num; // Pós=incremento
echo ("$num <br>"); // Output de $num incrementada

--$num; //Pré-decremento de $num
echo ("$num <br>"); // output de $num decrementada

$num--; // Pós-decremento de $num
echo ("$num <br>"); //output de $num decremenada


//============= Relacionais - são utilizados para realizar comparações entre empressões =============
/*
        ==   | Igual. (Resulta em TRUE se as expressões forem iguais e FALSE se forem diferentes)
       ===   | Idêntico. (Resulta em TRUE se as expressões e tipo de dado forem iguais e FALSE se forem diferentes)
    != ou <> | Diferente 
         <   | Menor
         >   | Maior
        <=   | Menor ou igual
        >=   | maior ou igual
    */


//============= Lógicos =============
/*
    ($a and $b) | AND (E) (só é verdade se $a E $b forem TRUE)
    ($a or $b)  | OR (OU) (É verdadeiro se $a OU $b forem TRUE)
    ($a xor $b) | XOR (OU exclusivo, só é veradeiro se uma das variáveis for TRUE e a OUTRA FALSE)
    (! $a)      | NOT (Verdadeiiro se $a for FALSE)
    ($a && $b)  | AND (E) (outra sintaxe)
    (a || $b)   | OR (OU) (outra sintaxe)
*/

?>