var canvas = document.getElementById("mycanvas");
var ctx = canvas.getContext("2d");

var color = Object;
color.Neutral="#d8ba7c";
color.Neutral_True="#b88a6c";
color.Neutral_Axis="#6A5B3D";
color.Neutral_Allies="3d8ba7c";
color.Italy="#58360E";
color.British="#916400";
color.UK_Europe="#916400";
color.UK_Pacific="#8E6600";
color.France="#156CC4";
color.Impassable="#d8ba7c";
color.America="#5a5a00";
color.Russia="#af2828";
color.Germany="#5a5a5a";
color.Japan="#DCB53F";
color.China="#713778";
color.Canada="#bf0010";
color.ANZAC="#4d7f7f";
color.Dutch="#ff6d00";
color.Mongolia="#d8ba7c";


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
    // Draw background country colors
    for (i = 0; i < polygons.length; i++) {
        drawTerritory(true, polygons[i]);
    }
    // Draw territory boundaries
    for (i = 0; i < polygons.length; i++) {
        drawTerritory(false, polygons[i]);
    }
}

function drawTerritory(fill, territory) {
    ctx.fillStyle = selectColor(territory.name);
    var i;
    for (i = 0; i < territory.landmasses.length; i++) {
        var coordinates = territory.landmasses[i].coordinates;
        var j;
        ctx.beginPath();
        ctx.moveTo(coordinates[0][0],coordinates[0][1]);
        for (j = 0; j < coordinates.length; j++) {
            ctx.lineTo(coordinates[j][0],coordinates[j][1]);
        }
        if (fill) {
            ctx.fill('evenodd');
        } else {
            ctx.strokeStyle = "#ffffff";
            ctx.lineWidth = 5;
            console.log("stroking");
            ctx.stroke();
        }
    }
}

// take player data and pick player color, or for now just random
function selectColor(territoryName) {
    if (territoryName.search("Sea Zone") !== -1) {
        return "#0000ff";
    } else {
        return color[placements[territoryName].occupier];
    }
}