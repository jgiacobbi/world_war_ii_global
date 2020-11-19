var polygons = Object;
var placements = Object;

function loadPolygons() {
    ws.onmessage = function(evt) {
        var response = JSON.parse(evt.data);
        polygons = JSON.parse(response);
        alert('polygons loaded');
        console.log(polygons);
        drawMap();
        loadPlacements();
    };

    socketSend('loadPolygons');
}

function loadPlacements() {
    ws.onmessage = function(evt) {
        var response = JSON.parse(evt.data);
        placements = JSON.parse(response);
        alert('placements loaded');
        console.log(placements);
        drawMap();
    };

    socketSend('loadPlacements');
}

$(document).ready( function() {
    // Should initiate ws var
    InitiateWebSocketConnection('beef', 'beefpass', function() {
        loadPolygons();
    });
});