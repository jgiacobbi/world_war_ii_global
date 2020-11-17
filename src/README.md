json message format

{
    "method": "METHOD",
    "payload": {
        "field 1": "CONTENTS",
        "field N": "CONTENTS"
    }
}

{
    "method": "lobbyMessage",
    "payload": {
        "lobby": "LOBBY_NAME",
        "message": "MESSAGE"
    }
}

{
    "method": "lobbyMessage",
    "payload": [
        "lobby": "LOBBY_NAME",
        "from": "USER_NAME",
        "time": UNIX_TIME,
        "message": "MESSAGE"
    ]
}