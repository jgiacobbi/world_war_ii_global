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
window.lobbyName = ''; // not actually sure of required scope, leaving everything global for debugging

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
    // TODO:: backend shoudl return reconnect tokenry for browser to store for reconnects
    await wsp.RequestResponse({ method: 'setPlayerName', payload: {name: window.playerName}})
    $("#nameForm").hide();
    $("#lobbyForm").show();
}

async function loadExistingLobbies() {
    lobbyList = await wsp.RequestResponse({ method: 'listLobbies'});
    console.log(lobbyList);
    $("#existingLobbies").html(lobbyList);
    $("#existingLobbies").show();
}

async function addToLobby() {
    // TODO:: Should we add some sort of lobby/token id? or is player/connect-id sufficient? just thinking mid-game name changes to "slayer of dave"
    await wsp.RequestResponse({method: 'addToLobby', payload: {lobbyName: window.lobbyName}});

    // Hide lobby bullshit now we have playerName/lobbyName
    $('#lobbyDiv').hide();

    // The above will block until this gets called right?  if so we can just use the same call for loading new or joining existing games
    loadInitMapData();
}

$(document).ready(async function () {
    await wsp.open();
    // Lobbying
    $('#nameButton').on('click', function () {
        window.playerName = $('#name').val();
        setPlayerName();
        loadExistingLobbies();
    });
    $('#lobbyButton').on('click', function () {
        window.lobbyName = $('#lobby').val();
        addToLobby();
    });

});