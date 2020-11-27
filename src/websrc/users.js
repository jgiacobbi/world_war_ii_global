import { loadInitMapData } from './mapDrawing.js';

async function joinGame(gameName) {
    try {
        await wsp.RequestResponse({method: 'joinGame', payload: {name: gameName}});
        return true;
    } catch (meatErr) {
        return false;
    }
}

export async function loadExistingGames() {
    try {
        gameList = await wsp.RequestResponse({ method: 'listGames'});
        if (gameList.length > 0) {
            $("#existingGames").html(gameList.join('<br>'));
        }
        
    } catch(meatErr) {
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
    loginReport = {
        inGame : false,
        login : true
    }

    try {
        response = await wsp.RequestResponse({ method: 'login', payload: payloadObj});

        expirationDate = new Date(response.expiry * 1000);
        Cookies.set('axis-key', response.key, { expires: expirationDate });
        loginReport.inGame = response.inGame;
    } catch (meatErr) {
        loginReport.login = false;
    }

    return loginReport;
}

export async function moveToGame() {
    $('#startDiv').hide();
    await loadInitMapData();
}

export function assembleStartDiv() {
    $('#startDiv').html(startDivContents());

    $('#nameButton').on('click', async function () {
        loginReport = await loginWithName($('#name').val());

        if (loginReport.login) {
            inGameCheck(loginReport.inGame);
        } else {
            alert("Failed to log in, this shouldn't fail");
        }
    });

    $('#gameButton').on('click', async function () {
        if (await joinGame($('#game').val())) {
            moveToGame();
        } else {
            alert('Failed to join game');
        }
    });
}

export async function start() {
    key = Cookies.get('axis-key');

    if (typeof key !== 'undefined') {
        loginReport = await loginWithKey(key);

        if (loginReport.login) {
            inGameCheck(loginReport.inGame);
        } else {
            alert("Failed to log in, deleting cookies.");
            Cookies.remove('axis-key');
            $('#startDiv').show();
            $('#nameForm').show();
        }
    } else {
        $('#startDiv').show();
        $('#nameForm').show();
    }
}

function inGameCheck(inGame) {
    if (inGame) {
        moveToGame();
    } else {
        $('#startDiv').show();
        $("#nameForm").hide();
        $("#gameForm").show();
        loadExistingGames();
    }
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