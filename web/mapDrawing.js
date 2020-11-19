var canvas = document.getElementById("mycanvas");
var ctx = canvas.getContext("2d");

// Set background blue for lakes
ctx.fillStyle = "blue";
ctx.fillRect(0, 0, canvas.width, canvas.height);

var color = {
    "Neutral": "#d8ba7c",
    "Neutral_True": "#b88a6c",
    "Neutral_Axis": "#6A5B3D",
    "Neutral_Allies": "3d8ba7c",
    "Italy": "#58360E",
    "British": "#916400",
    "Britain": "#916400",
    "UK_Europe": "#916400",
    "UK_Pacific": "#8E6600",
    "France": "#156CC4",
    "Impassable": "#d8ba7c",
    "America": "#5a5a00",
    "Russia": "#af2828",
    "Germany": "#5a5a5a",
    "Japan": "#DCB53F",
    "China": "#713778",
    "Canada": "#bf0010",
    "ANZAC": "#4d7f7f",
    "Dutch": "#ff6d00",
    "Mongolia": "#d8ba7c"
};


function drawCapitals() {
    var CapitalLocations = 
    {
        "United Kingdom": [1865,475],
        "Germany": [2470,667],
        "Southern Italy": [2365,1305],
        "Eastern United States": [715,655],
        "France": [2140,825],
        "Russia": [3340,470],
        "Himalayas": [4235,1453],
        "Japan": [5800,888],
        "New South Wales": [5930,2890],
        "India": [3930,2050],
        "Ontario": [490,350]
    }
    Object.keys(CapitalLocations).forEach(function(value, index) {
        ctx.beginPath();
        ctx.fillStyle = "red";
        ctx.arc(CapitalLocations[value][0], CapitalLocations[value][1], 2, 0, Math.PI, false);
        ctx.fill();
    });
}

function drawMap() {
    var i;
    var polygonKeys = Object.keys(polygons);
    // Draw Waters
    for (i = 0; i < polygonKeys.length; i++) {
        if (polygonKeys[i].search("Sea Zone") !== -1) {
            drawTerritory(true, polygons[polygonKeys[i]], polygonKeys[i]);
        }
    }
    // Draw background country colors
    for (i = 0; i < polygonKeys.length; i++) {
        if (polygonKeys[i].search("Sea Zone") === -1) {
            drawTerritory(true, polygons[polygonKeys[i]], polygonKeys[i]);
        }
    }
    // Draw territory and sea boundaries
    for (i = 0; i < polygonKeys.length; i++) {
        drawTerritory(false, polygons[polygonKeys[i]], polygonKeys[i]);
    }

    // Draw small red circles for capitals
    drawCapitals();
}

function drawTerritory(fill, territory, territoryName) {
    ctx.fillStyle = selectColor(territoryName);
    var i;
    for (i = 0; i < territory.length; i++) {
        var coordinates = territory[i];
        var j;
        ctx.beginPath();
        ctx.moveTo(coordinates[0][0],coordinates[0][1]);
        for (j = 0; j < coordinates.length; j++) {
            ctx.lineTo(coordinates[j][0],coordinates[j][1]);
        }
        ctx.lineTo(coordinates[0][0],coordinates[0][1]);
        if (fill) {
            ctx.fill('evenodd');
        } else {
            ctx.strokeStyle = "#ffffff";
            ctx.lineWidth = 1;
            ctx.stroke();
        }
    }
}

// take player data and pick player color, or for now just random
function selectColor(territoryName) {
    if (territoryName.search("Sea Zone") !== -1) {
        return "#0000ff";
    } else {
        if (placements.hasOwnProperty(territoryName)) {
            if (placements[territoryName].hasOwnProperty('occupier')) {
                return color[placements[territoryName].occupier];
            } else {
                return "#ffffff";
            }
        } else {
            return "#000000";
        }
    }
}