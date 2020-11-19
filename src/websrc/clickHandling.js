export default function(canvas) {
    canvas.addEventListener('click', function(e) {
        var bounds= canvas.getBoundingClientRect();
        var mouseXY = [e.clientX-bounds.left, e.clientY-bounds.top]
        var polygonKeys = Object.keys(polygons);
        for (i = 0; i < polygonKeys.length; i++) {
            var name = polygonKeys[i];
            var j;
            if (name.search("Sea Zone") === -1) {
                for (j = 0; j < polygons[name].length; j++) {
                    var coordinates = polygons[name][j];
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
        }
        for (i = 0; i < polygonKeys.length; i++) {
            var name = polygonKeys[i];
            var j;
            for (j = 0; j < polygons[name].length; j++) {
                var coordinates = polygons[name][j];
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
}
