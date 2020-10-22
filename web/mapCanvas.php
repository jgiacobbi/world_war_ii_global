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
    var i;
    for (i = 0; i < mapData.length; i++) {
        var name = mapData[i].name;
        var j;
        var x = Math.floor(Math.random() * 256);
        var y = Math.floor(Math.random() * 256);
        var z = Math.floor(Math.random() * 256);
        var bgColor = "rgb(" + x + "," + y + "," + z + ")";
        ctx.fillStyle = bgColor;

        for (j = 0; j < mapData[i].landmasses.length; j++) {
            var coordinates = mapData[i].landmasses[j].coordinates;
            var k;
            let region = new Path2D();
            region.moveTo(coordinates[0][0],coordinates[0][1]);
            for (k = 1; k < coordinates.length; k++) {
                    region.lineTo(coordinates[k][0],coordinates[k][1]);
            }
            ctx.strokeStyle = "#000000";
            ctx.lineWidth = 3;
            ctx.fill(region, 'evenodd');
            ctx.stroke();
        }
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
