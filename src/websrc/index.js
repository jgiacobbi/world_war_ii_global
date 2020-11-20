import GetWebSocket from './socket.js';
import { drawMap } from './mapDrawing.js';
import AddClickHandler from './clickHandling.js';
import $ from 'jquery';

window.$ = $;

//TODO fix global pollution
window.canvas = document.getElementById("mycanvas");
window.ctx = canvas.getContext("2d");
window.polygons = Object;
window.placements = Object;
window.wsp = GetWebSocket();

// Set background blue for lakes
ctx.fillStyle = "blue";
ctx.fillRect(0, 0, canvas.width, canvas.height);

AddClickHandler(canvas);

$(document).ready(async function() {
    await wsp.open();

    console.log("Calling for the meat");
    [polygons, placements] = await Promise.all(
      [
        wsp.RequestResponse({method: 'loadPolygons'}),
        wsp.RequestResponse({method: 'loadPlacements'})
      ]
    );

    console.log("Executing the meat");

    drawMap();
});