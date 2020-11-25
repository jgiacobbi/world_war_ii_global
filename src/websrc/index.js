import GetWebSocket from './socket.js';
import { drawMap } from './mapDrawing.js';
import AddClickHandler from './clickHandling.js';
import $ from 'jquery';

window.$ = $;

//TODO fix global pollution
window.canvas = document.getElementById('mycanvas'); // this doesn't need to be global
window.ctx = canvas.getContext('2d'); // this is only really required in mapDrawing.js, could scope it to there
window.polygons = Object; // mapDrawing and clickHandler need this - maybe we make map.js which handles map interactions
window.placements = Object; // mapDrawing and clickHandler, another candidate for map.js
window.wsp = GetWebSocket(); // we could probably scope this to index.js, and keep everything ui->backend-comms here?
window.playerName = ''; // not actually sure of required scope, leaving everything global for debugging
window.gameName = ''; // not actually sure of required scope, leaving everything global for debugging

AddClickHandler(canvas);

async function loadInitMapData() {
    [polygons, placements] = await Promise.all(
        [
            wsp.RequestResponse({ method: 'loadPolygons' }),
            wsp.RequestResponse({ method: 'loadPlacements' })
        ]
    );

    // Set background blue for lakes
    ctx.fillStyle = "blue";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    drawMap();
}

async function setPlayerName() {
    // TODO:: set a cookie, auto login if the cookie exists and is not expired
    // expiry is in unix time
    // return ["name" => $username, "key" => $key, "expiry" => $expiry];
    try {
        await wsp.RequestResponse({ method: 'login', payload: {username: window.playerName}})
    } catch (meatErr) {
        console.log('Meatiness while setting player name', meatErr);
    }
}

async function loadExistingGames() {
    try {
        gameList = await wsp.RequestResponse({ method: 'listGames'});
    } catch(meatErr) {
        console.log('Meatiness while loading existing games', meatErr);
    }

    $("#existingGames").html(gameList.join('<br>'));
    $("#existingGames").show();
}

async function joinGame() {
    try {
        await wsp.RequestResponse({method: 'joinGame', payload: {name: window.gameName}});
    } catch (meatErr) {
        console.log('Meatiness while adding player to game', meatErr);
    }

    // The above will block until this gets called right?  if so we can just use the same call for loading new or joining existing games
    loadInitMapData();
}

$(document).ready(async function () {
    await wsp.open();
    // Starting
    $('#nameButton').on('click', function () {
        window.playerName = $('#name').val();
        setPlayerName();
        $("#nameForm").hide();
        $("#gameForm").show();
        loadExistingGames();
    });
    $('#gameButton').on('click', function () {
        window.gameName = $('#game').val();
        joinGame();
        $('#startDiv').hide();
    });

});