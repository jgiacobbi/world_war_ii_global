var polygons = Object;
var placements = Object;

function loadPolygons() {
    $.get("../api/polygons", function(data, status) {
        polygons = data;
        drawMap();
    });
}

function loadPlacements() {
    $.get("../api/placements", function(data, status) {
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
