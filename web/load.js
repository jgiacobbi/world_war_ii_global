var polygons = Object;
var placements = Object;

function loadPolygons() {
    meatRequest('loadPolygons', function(data) {
        polygons = data;
        drawMap();
    });
}

function loadPlacements() {
    meatRequest('loadPlacements', function(data) {
        placements = data;
        drawMap();
    });
}

$(document).ready( function() {
    loadPolygons();
    loadPlacements();
    drawMap();
    drawCapitals();
});
