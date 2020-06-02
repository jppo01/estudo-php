
<?php
/*
    Livro: PHP: programando com Orientação a Objeto 
    Autor: Pablo Dall'Oglio
    Edição: 3ª 
    Capítulo de estudo: Cap. 01

*/
  
// Variable Variables - Usamos quando precisamos ter em nossos códigos nomes de variáveis que mudam de acordo com determinada situação

$variavel = 'nome'; // declarando uma varivel com conteúdo nome
$$variavel = 'maria'; // usando o conceito de variable variables para fazer nome virar variável e receber maria
echo "nome é: $nome"."<br>"; //imprimindo na página o resultado


//Criando referência entre variáveis usando o operador &
$a=5; //declarando uma variavel $a recebendo 5
$b=&$a; //criando referência entre $b e $a usando o operador &
$b=10; // atribuindo 10 a $b para teste
echo "valor de a é $a e o valor de b é $b <br>"; // printando o resultado
// obs: objetos sempre são copiados por referencia independente do operador &


// Tipo booleano: exmpressa um valor lógico. 1 ou 0, true ou false, high ou low etc
$a = TRUE; // exemplo

// Tipo númerico: podem receber valores em notação decimal, hexadecimal ou octal.
$a = 1981; //exemplo

// tipo string: é uma cadeia de caracteres alfanuméricos
$a = 'isso é uma string'; //exemplo

//Tipo Array: é uma lista de valores de valores que podem ser de tipos diferentes
$a = array('Potência', 'Corrente', 'Tensão'); // exemplo


//Objeto: é uma entidade com um determinado comportamento definido por seus métodos (ações) e propriedades (dados)


// Recurso (resource): é uma variável que mantém uma referência de um recurso externo


// Tipo misto é uma identificação que representa diversos tipos de dados que podem ser usados em um mesmo parâmetro.


// Tipo callback pode ser o nome de uma function representada por uma string ou método de um objeto a ser
// executado por um array.


// Tipo NULL: significa q a variável não tem valor.




?>