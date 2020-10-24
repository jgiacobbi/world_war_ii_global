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