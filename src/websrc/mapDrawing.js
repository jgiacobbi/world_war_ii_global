export default {
    color: {
        "Neutral": "#d8ba7c",
        "Neutral_True": "#b88a6c",
        "Neutral_Axis": "#6A5B3D",
        "Neutral_Allies": "#d8ba7c",
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
    },

    CapitolLocations: {
        "United Kingdom": [2045,625],
        "Germany": [2630,770],
        "Southern Italy": [2358,1260],
        "Eastern United States": [670,750],
        "France": [2110,853],
        "Russia": [3515,590],
        "Himalayas": [4235,1453],
        "Japan": [5780,967],
        "New South Wales": [5990,3005],
        "India": [4080,1850],
        "Ontario": [600,605],
    },

    VictoryLocations: {
        "Egypt": [2928,1680],
        "Poland": [2755,770],
        "Volgograd": [3540,1085],
        "Novgorod": [2925,390],
        "Kwangtung": [4795,1795],
        "Philippines": [5213,1940],
        "Kiangsu": [5000,1410],
        "Western United States": [7460,655],
        "Hawaiian Islands": [6900,1515],
    },

    drawCities: function() {
        for(let [key,value] of Object.entries(this.CapitolLocations)) {
            ctx.beginPath();
            ctx.fillStyle = "red";
            ctx.arc(value[0], value[1], 6, 0, 2 * Math.PI, false);
            ctx.fill();
        }

        size = 8;
        for(let [key,value] of Object.entries(this.VictoryLocations)) {
            ctx.fillRect(value[0] - (size/2), value[1] - (size/2), size, size);
        }
    },

    drawPlaceLocations: function() {
        console.log("Drawing place locations");
        console.log(`Squares with placement coords: ${Object.values(placeCoords).length}`);
        for(let [country, list] of Object.entries(placeCoords)) {
            console.log(`${list.length} entries for ${country}`);
            list.forEach(function(item, index) {
                ctx.beginPath();
                ctx.fillStyle = "white";
                ctx.arc(item[0], item[1], 3, 0, 2 * Math.PI, false);
                ctx.fill();
            })
        }
    },

    init: async function() {
        [polygons, placements, placeCoords] = await Promise.all(
            [
                wsp.RequestResponse({ method: 'loadPolygons' }),
                wsp.RequestResponse({ method: 'loadPlacements' }),
                wsp.RequestResponse({ method: 'loadPlaceCoordinates' })
            ]
        );
    
        // Set background blue for lakes
        ctx.fillStyle = "blue";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        this.drawMap();
    },

    drawMap: function() {
        var i;
        var polygonKeys = Object.keys(polygons);
    
        // Draw Waters
        for (i = 0; i < polygonKeys.length; i++) {
            if (polygonKeys[i].search("Sea Zone") !== -1) {
                this.drawTerritory(true, polygons[polygonKeys[i]], polygonKeys[i]);
            }
        }
        // Draw background country colors
        for (i = 0; i < polygonKeys.length; i++) {
            if (polygonKeys[i].search("Sea Zone") === -1) {
                this.drawTerritory(true, polygons[polygonKeys[i]], polygonKeys[i]);
            }
        }
        // Draw territory and sea boundaries
        for (i = 0; i < polygonKeys.length; i++) {
            this.drawTerritory(false, polygons[polygonKeys[i]], polygonKeys[i]);
        }
    
        // Draw small red circles for capitals
        this.drawCities();

        this.drawPlaceLocations();
    },

    drawTerritory: function(fill, territory, territoryName) {
        ctx.fillStyle = this.selectColor(territoryName);
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
    },
    selectColor: function(territoryName) {
        if (territoryName.search("Sea Zone") !== -1) {
            return "#0000ff";
        } else {
            if (placements.hasOwnProperty(territoryName)) {
                if (placements[territoryName].hasOwnProperty('occupier')) {
                    return this.color[placements[territoryName].occupier];
                } else {
                    return "#ffffff";
                }
            } else {
                return "#000000";
            }
        }
    }
}
