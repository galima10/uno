document.addEventListener("turbo:load", () => {
    window.playTimeouts = [];

    function playTimeout(fn, delay) {
        const id = setTimeout(fn, delay);
        window.playTimeouts.push(id);
        return id;
    }

    function clearPlayTimeouts() {
        window.playTimeouts.forEach(clearTimeout);
        window.playTimeouts = [];
    }

    const gameDataElement = document.getElementById("game-data");
    if (!gameDataElement) return;

    const gameData = gameDataElement.dataset;

    // On récupère les valeurs et on les parse si nécessaire
    const userBaseUrl = gameData.userBaseUrl || null;
    const enemyBaseUrl = gameData.enemyBaseUrl || null;
    const deckBaseUrl = gameData.deckBaseUrl || null;

    const turnId = gameData.turnId;
    const turnPlayerType = gameData.turnPlayerType;
    const cardAngle = gameData.cardAngle;
    const winner = gameData.winner === "null" ? null : gameData.winner;
    const enemyCardPlayed =
        gameData.enemyCardPlayed === "null" ? null : gameData.enemyCardPlayed;
    const cardPicked = JSON.parse(gameData.cardPicked);

    const deck = document.querySelector("#deck");

    // Logique JS basée sur les données
    if (winner === null) {
        if (turnPlayerType === "enemy") {
            if (enemyCardPlayed) {
                playTimeout(() => {
                    enemyPlaceCard(turnId, enemyCardPlayed, cardAngle);
                    const url = enemyBaseUrl.replace("10", cardAngle);
                    playTimeout(() => {
                        window.location.href = url;
                    }, 700);
                }, 3000);
            } else if (cardPicked === false) {
                playTimeout(() => {
                    pickCard();
                    playTimeout(
                        () => (window.location.href = deckBaseUrl),
                        200,
                    );
                }, 3000);
            } else {
                playTimeout(() => location.reload(), 500);
            }
        } else {
            if (cardPicked === null) {
                console.log("peut pas jouer");
                playTimeout(() => location.reload(), 3000);
            }
        }
    } else {
        playTimeout(() => (window.location.href = "/win"), 3000);
    }

    if (!window.turboDeckListenerAdded) {
        document.addEventListener("click", handleDeck);
        window.turboDeckListenerAdded = true;
    }

    function handleDeck(e) {
        if (deck && (e.target === deck || deck.contains(e.target))) {
            if (cardPicked === false && turnPlayerType === "user") {
                pickCard();
                playTimeout(() => {
                    window.location.href = deckBaseUrl;
                }, 200);
            }
        }
    }

    const userCardsPlayable = document.querySelectorAll(".can-play");
    const userArea = document.querySelector("#user-area");

    if (
        userCardsPlayable.length === 0 &&
        cardPicked &&
        turnPlayerType === "user"
    ) {
        location.reload();
    }

    userCardsPlayable.forEach((button) => {
        button.addEventListener(
            "click",
            () => {
                if (!button.classList.contains("user-card-played")) {
                    const card = button.querySelector(".card");
                    const cardId = card.id.slice(5);
                    placeCardOnDiscard(button, card, cardAngle);

                    button.classList.add("user-card-played");
                    userArea.style.pointerEvents = "none";
                    const url = userBaseUrl.replace(
                        "100-10",
                        `${cardId}-${cardAngle}`,
                    );

                    playTimeout(() => {
                        window.location.href = url;
                    }, 500);
                }
            },
            { once: true },
        );
    });

    function pickCard() {
        console.log("pioche");
        const animationCardContainer = document.querySelector(
            ".game-animation-container .card-container",
        );
        const gamePlayers = document.querySelector(".game-players");
        const animationCard = document.createElement("div");
        animationCard.classList.add("deck-card");

        const deckCenterDeltaX =
            gamePlayers.getBoundingClientRect().width / 2 -
            deck.getBoundingClientRect().left -
            deck.getBoundingClientRect().width / 2 -
            24 +
            24;
        const deckCenterDeltaY =
            gamePlayers.getBoundingClientRect().height / 2 -
            deck.getBoundingClientRect().top -
            deck.getBoundingClientRect().height / 2 +
            1.5;

        let transform = `translate3d(${-deckCenterDeltaX}px, ${-deckCenterDeltaY}px, 0) rotateX(45deg)`;
        animationCardContainer.style.transform = transform;
        animationCard.style.transform = "rotate(-45deg)";
        animationCardContainer.appendChild(animationCard);

        const allPlayersElement = document.querySelectorAll(".player-cards");

        const playerCenterDeltaY =
            gamePlayers.getBoundingClientRect().height / 2 -
            allPlayersElement[turnId].getBoundingClientRect().top -
            allPlayersElement[turnId].getBoundingClientRect().height / 2;
        const playerCenterDeltaX =
            gamePlayers.getBoundingClientRect().width / 2 -
            allPlayersElement[turnId].getBoundingClientRect().left -
            allPlayersElement[turnId].getBoundingClientRect().width / 2;

        let finalTransform;
        if (turnId === "0" || turnId === "2") {
            finalTransform = `translate3d(${playerCenterDeltaX}px, ${-playerCenterDeltaY}px, 0)`;
        } else {
            finalTransform = `translate3d(${-playerCenterDeltaX}px, ${-playerCenterDeltaY}px, 0)`;
        }

        playTimeout(() => {
            animationCardContainer.style.transition = "transform .5s";
            animationCard.style.transition = "transform .5s";
            animationCardContainer.style.transform = finalTransform;
            animationCard.style.transform = "";
        }, 1);
    }

    function enemyPlaceCard() {
        const animationCardContainer = document.querySelector(
            ".game-animation-container .card-container",
        );
        const enemyElement = document.querySelector(`#enemy-cards${turnId}`);
        const card = enemyElement.querySelector(`#card-${enemyCardPlayed}`);
        const cardNumber = card.querySelector(".card-front p").textContent;
        const cardClassColor = card.querySelector(".card-front").classList[1];
        const animationCard = document.createElement("div");
        const gamePlayers = document.querySelector(".game-players");
        animationCard.innerHTML += `
        <div class="card-inner">
            <div class="card-front ${cardClassColor}">
                <p>
                    ${cardNumber}
                </p>
            </div>
            <div class="card-back"></div>
        </div>
    `;
        const cardInner = animationCard.querySelector(".card-inner");
        animationCard.classList.add("card");

        const enemyElementCenterDeltaX =
            gamePlayers.getBoundingClientRect().width / 2 -
            enemyElement.getBoundingClientRect().left -
            enemyElement.getBoundingClientRect().width / 2 -
            24;
        const enemyElementCenterDeltaY =
            gamePlayers.getBoundingClientRect().height / 2 -
            enemyElement.getBoundingClientRect().top -
            enemyElement.getBoundingClientRect().height / 2;

        let transform = `translate3d(${-enemyElementCenterDeltaX}px, ${-enemyElementCenterDeltaY}px, 0)`;
        let finalTransform;
        const finalRotate = `rotate(${cardAngle}deg)`;

        if (turnId === "1") {
            transform = transform + " rotate(90deg)";
            finalTransform = `rotate(90deg)`;
        } else if (turnId === "2") {
            transform = transform + " rotate(180deg)";
            finalTransform = `rotate(180deg)`;
        } else {
            transform = transform + " rotate(270deg)";
            finalTransform = `rotate(270deg)`;
        }

        console.log(finalTransform);

        animationCard.style.transform = transform;
        cardInner.style.transform = finalRotate;
        animationCardContainer.appendChild(animationCard);
        animationCard.style.transition = ".5s";
        playTimeout(() => {
            animationCard.style.transform = finalTransform;
        }, 200);
    }

    function placeCardOnDiscard(cardContainer, card) {
        const discard = document.querySelector(".discard");
        const gameBoard = document.querySelector(".game-board");
        // Récupère les rectangles
        const discardRect = discard.getBoundingClientRect();
        const boardRect = gameBoard.getBoundingClientRect();
        const cardRect = cardContainer.getBoundingClientRect();

        // Calcul du centre de chaque élément
        const discardCenterX =
            discardRect.left - boardRect.left + discardRect.width / 2;
        const discardCenterY =
            discardRect.top - boardRect.top + discardRect.height / 2;
        const cardCenterX = cardRect.left - boardRect.left + cardRect.width / 2;
        const cardCenterY = cardRect.top - boardRect.top + cardRect.height / 2;

        // Calcul du delta pour que le centre corresponde
        const deltaX = discardCenterX - cardCenterX;
        const deltaY = discardCenterY - cardCenterY;

        card.querySelector(".card-inner").style.transform =
            `rotate(${cardAngle}deg)`;
        cardContainer.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0)`;
    }
});
