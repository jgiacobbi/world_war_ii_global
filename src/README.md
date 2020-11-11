json message format

{
    "message": "MESSAGE_TYPE",
    "payload": {
        "field 1": "CONTENTS",
        "field N": "CONTENTS"
    }
}

{
    "message": "lobby",
    "payload": {
        "lobby": "LOBBY_NAME",
        "message": "MESSAGE"
    }
}

{
    "message": "lobby",
    "payload": [
        "lobby": "LOBBY_NAME",
        "from": "USER_NAME",
        "time": UNIX_TIME,
        "message": "MESSAGE"
    ]
}