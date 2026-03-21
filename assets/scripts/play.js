const gameData = document.getElementById("game-data").dataset;

// On récupère les valeurs et on les parse si nécessaire
const winner = JSON.parse(gameData.winner);
const turnPlayerType = gameData.turnPlayerType;
const turnId = gameData.turnId;
const enemyCardPlayed = JSON.parse(gameData.enemyCardPlayed);
const cardPicked = JSON.parse(gameData.cardPicked);

// Logique JS basée sur les données
if (winner === null) {
    if (turnPlayerType === "enemy") {
        if (enemyCardPlayed) {
            setTimeout(() => (window.location.href = `/enemy/${turnId}`), 3000);
        } else if (cardPicked === false) {
            setTimeout(() => (window.location.href = `/deck/${turnId}`), 3000);
        } else {
            setTimeout(() => location.reload(), 3000);
        }
    } else {
        // tour du joueur
        if (cardPicked === null) {
            console.log("peut pas jouer");
            setTimeout(() => location.reload(), 3000);
        }
    }
} else {
    setTimeout(() => (window.location.href = "/win"), 3000);
}
