<html>
   <head>
      <style>
         #mycanvas{border:1px solid red;}
      </style>
   </head>
   <body>
      <canvas id="mycanvas" width="7705" height="3213"></canvas>
   </body>
<script>
    var mapData = <?php echo file_get_contents("polygons.json"); ?>;
    var canvas = document.getElementById("mycanvas");
    var ctx = canvas.getContext("2d");
    drawMap();

    function drawMap() {
        var i;
        // Draw background country colors
        for (i = 0; i < mapData.length; i++) {
            drawTerritory(true, mapData[i]);
        }
        // Draw territory backgrounds
        for (i = 0; i < mapData.length; i++) {
            drawTerritory(false, mapData[i]);
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
            var x = 0;
            var y = 0;
            var z = 255;
        } else {
            var x = Math.floor(Math.random() * 256);
            var y = Math.floor(Math.random() * 256);
            var z = Math.floor(Math.random() * 256);
        }
        return "rgb(" + x + "," + y + "," + z + ")";
    }

    canvas.addEventListener('click', function(e) {
        var bounds= canvas.getBoundingClientRect();
        var mouseXY = [e.clientX-bounds.left, e.clientY-bounds.top]
        for (i = 0; i < mapData.length; i++) {
            var name = mapData[i].name;
            var j;
            for (j = 0; j < mapData[i].landmasses.length; j++) {
                var coordinates = mapData[i].landmasses[j].coordinates;
                var k;
                let region = new Path2D();
                region.moveTo(coordinates[0][0],coordinates[0][1]);
                for (k = 1; k < coordinates.length; k++) {
                    region.lineTo(coordinates[k][0],coordinates[k][1]);
                }
                if(ctx.isPointInPath(region, mouseXY[0], mouseXY[1], 'evenodd')) {
                    alert('User Clicked: ' + name + ' (' + mouseXY[0] + ',' + mouseXY[1] + ')');
                    return;
                }
            }
        }

    });
    </script>
</html>
