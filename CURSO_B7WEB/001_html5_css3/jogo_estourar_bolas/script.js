function addBall(){
    var ball = document.createElement("div");
    ball.setAttribute("class", "bola");
    var positionX = Math.floor(Math.random() * 500);
    var positionY = Math.floor(Math.random() * 500);
    ball.setAttribute("style", "left: " + positionX + "px;" + "top: " + positionY + "px;");
    ball.setAttribute("Onclick", "estourar(this)");
    document.body.appendChild(ball);
}

function estourar(ballElement){
    document.body.removeChild(ballElement);
}

function startGame(){
    setInterval(addBall, 1000);
}