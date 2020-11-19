const WebSocketAsPromised = require('websocket-as-promised');

export default function() {
    var wsp = new WebSocketAsPromised(
      `ws://${window.location.hostname}:8080/`,
      {
          packMessage: data => JSON.stringify(data),
          unpackMessage: data => JSON.parse(data),
          attachRequestId: (data, requestId) => Object.assign({id: requestId}, data), // attach requestId to message as `id` field
          extractRequestId: data => data && data.id
      }
    );

    //debug stuff
    //wsp.onResponse.addListener(data => console.log(data));

    return wsp;
}

/*var ws;
var key;

async function InitiateWebSocketConnection(user, password, callback) {
    // Let us open a web socket
    ws = new WebSocket("ws://" + window.location.hostname + ":8080/");

    ws.onopen = function() {
        socketSend('login', {'user': user, 'password': password});
    }
    
    ws.onmessage = function(evt) {
        console.log(evt);
        setKey(evt);
        callback();
    }
    
    ws.onclose = function(evt) {
        alert('ws closed');
        console.log(evt);
    };
}

function socketSend(method, payload) {
    var dataToSend = JSON.stringify({'key': key, 'method': method, 'payload': payload});
    ws.send(dataToSend);
}

function setKey(evt) {
    if (evt.data.key) {
        key = evt.data.key;
    }
}
*/