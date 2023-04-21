<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pessoas</title>
</head>
<body>

     {{-- FEITO COM @ Component: --}}
    <div style="
        display: flex;
        flex-direction:row" >
        @foreach ($peoples as $people)
            @component('Components.peoplelist')

            @slot('img')
            {{$people['img']}}
            @endslot

            @slot('name')
            {{$people['name']}}
            @endslot

            @slot('age')
            {{$people['age']}}
            @endslot

            @slot('birth')
            {{$people['birth']}}
            @endslot

            @endcomponent

        @endforeach

    </div>

    <hr>

    {{-- FEITO COM INCLUDE --}}
    <div style="
        display: flex;
        flex-direction:row" >
        @foreach($peoples as $people)
        @include('Components.peoplelist', $people)
        @endforeach
    </div>

</body>
</html>
