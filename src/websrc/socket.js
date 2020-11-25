const WebSocketAsPromised = require('websocket-as-promised');

//there might be a better way to do this but the docs are sparse
WebSocketAsPromised.prototype.RequestResponse = async function(request) {
    var body;
    await this.sendRequest(request)
        .then(response => {
            if (response.hasOwnProperty('body')) {
                body = response.body;
            } else if (response.hasOwnProperty('error')) {
                throw response.error;
            } else {
                throw 'Unknown error performing request';
            }
        });

    return body;
};

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
};
