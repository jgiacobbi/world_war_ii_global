const WebSocketAsPromised = require('websocket-as-promised');
//const Sockette = require('sockette');

function buildWebsocketURL() {
  return `ws://${window.location.hostname}:8080/`;
}

const wsp = new WebSocketAsPromised(buildWebsocketURL(), {
  packMessage: data => JSON.stringify(data),
  unpackMessage: data => JSON.parse(data),
  attachRequestId: (data, requestId) => Object.assign({id: requestId}, data), // attach requestId to message as `id` field
  extractRequestId: data => data && data.id
});
/*const wsp = new WebSocketAsPromised('ws://135.181.47.219:8080', {
    createWebSocket: url => Sockette.default(url, {
        timeout: 5e3,
        maxAttempts: 10,
        onopen: e => console.log('Connected!', e),
        onmessage: e => console.log('Received:', e),
        onreconnect: e => console.log('Reconnecting...', e),
        onmaximum: e => console.log('Stop Attempting!', e),
        onclose: e => console.log('Closed!', e),
        onerror: e => console.log('Error:', e)
    })
});*/

wsp.onResponse.addListener(data => console.log(data));
wsp.onUnpackedMessage.addListener(data => console.log(data.error));

async function homestyleCookery() {
    await wsp.open();
    await meat();
    meatRequest('braisery', function() {console.log('brahaizedbrah')});
}

async function meat() {
  try {
    wsp.sendPacked({meat: 'message'});
  } catch(e) {
    console.error(e);
  }
}

//TODO: what's the async equivalent for the response?
function meatRequest(method, callback) {
    try {
      var requestMeat = {'method': method};
        wsp.sendRequest(requestMeat)
            .then(
              function(response) {
                response => console.log(response);
                callback(response);
              });
    } catch (e) {
        console.error(e);
    }
}

homestyleCookery();