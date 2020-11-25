import { loadInitMapData } from './mapDrawing.js';

async function joinGame(gameName) {
    try {
        await wsp.RequestResponse({method: 'joinGame', payload: {name: gameName}});
    } catch (meatErr) {
        console.log('Meatiness while adding player to game', meatErr);
    }
}

export async function loadExistingGames() {
    try {
        gameList = await wsp.RequestResponse({ method: 'listGames'});
        if (gameList.length > 0) {
            $("#existingGames").html(gameList.join('<br>'));
        }
        
    } catch(meatErr) {
        console.log('Meatiness while loading existing games', meatErr);
        $("#existingGames").html("Error loading games");
    }

    $("#existingGames").show();
}

export async function loginWithName(playerName) {
    return await login({username: playerName});
}

export async function loginWithKey(session_key) {
    return await login({key: session_key});
}

async function login(payloadObj) {
    try {
        response = await wsp.RequestResponse({ method: 'login', payload: payloadObj});
    } catch (meatErr) {
        console.log('Meatiness while logging in with ' + payloadObj, meatErr);
    }

    expirationDate = new Date(response.expiry * 1000);
    Cookies.set('axis-key', response.key, { expires: expirationDate });
    return response.inGame;
}

export async function moveToGame() {
    $('#startDiv').hide();
    await loadInitMapData();
}

export function assembleStartDiv() {
    $('#startDiv').html(startDivContents());

    $('#nameButton').on('click', async function () {
        inGame = await loginWithName($('#name').val());
        if (inGame) {
            moveToGame();
        } else {
            $("#nameForm").hide();
            $("#gameForm").show();
            loadExistingGames();  
        }
    });

    $('#gameButton').on('click', async function () {
        await joinGame($('#game').val());
        moveToGame();
    });
}

function startDivContents() {
    return `
        <div id="nameForm" style="display:none">
            <label for="name">Enter Name:</label>
            <input type="text" id="name"><br><br>
            <input type="submit" value="Submit Name" id="nameButton">
        </div>
        <div id="gameForm" style="display:none">
            <label for="game">Enter Game:</label>
            <input type="text" id="game"><br><br>
            <input type="submit" value="Submit Game" id="gameButton">
        </div>
        <div id="existingGames" style="display:none">
            No Games Currently Exist
        </div>
    `
}