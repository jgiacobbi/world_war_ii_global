const WebSocketAsPromised = require('websocket-as-promised');
//const Sockette = require('sockette');

document.getElementById("root").innerHTML = "WHAT";

const wsp = new WebSocketAsPromised('ws://135.181.47.219:8080', {
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
    meatRequest();
}

async function meat() {
  try {
    wsp.sendPacked({meat: 'message'});
  } catch(e) {
    console.error(e);
  }
}

//TODO: what's the async equivalent for the response?
function meatRequest() {
    try {
        wsp.sendRequest({method: 'braise'})
            .then(response => console.log(response));
    } catch (e) {
        console.error(e);
    }
}

homestyleCookery();