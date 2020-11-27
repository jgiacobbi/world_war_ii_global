import GetWebSocket from './socket.js';
import AddClickHandler from './clickHandling.js';
import * as users from './users.js';
import $ from 'jquery';
import Cookies from 'js-cookie'

window.$ = $;
window.Cookies = Cookies;

//TODO fix global pollution
window.canvas = document.getElementById('mycanvas'); // this doesn't need to be global
window.ctx = canvas.getContext('2d'); // this is only really required in mapDrawing.js, could scope it to there
window.polygons = Object; // mapDrawing and clickHandler need this - maybe we make map.js which handles map interactions
window.placements = Object; // mapDrawing and clickHandler, another candidate for map.js
window.wsp = GetWebSocket(); // we could probably scope this to index.js, and keep everything ui->backend-comms here?
window.playerName = ''; // not actually sure of required scope, leaving everything global for debugging
window.gameName = ''; // not actually sure of required scope, leaving everything global for debugging

AddClickHandler(canvas);

$(document).ready(async function () {
    openPromise = wsp.open();

    users.assembleStartDiv();

    await openPromise;

    users.start();
});