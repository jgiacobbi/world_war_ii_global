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
    await Promise.all(
      [
        wsp.sendRequest({method: 'loadPolygons'})
          .then(response => {
            polygons = JSON.parse(response.response);
          }),
        wsp.sendRequest({method: 'loadPlacements'})
          .then(response => {
            placements = JSON.parse(response.response);
          })
      ]
    );

    console.log("Executing the meat");

    drawMap();
});