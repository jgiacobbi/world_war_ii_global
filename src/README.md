## JSON Message Format

```
{
    "method": "METHOD",
    "payload": {
        "field 1": "CONTENTS",
        "field N": "CONTENTS"
    }
}
```
### Login
Register:
```
{
    "method": "register,
    "payload": {
        "username": "USERNAME",
        "password": "PASSWORD"
    }
}

{
    "success": true
}
```

Login (with credentials):
```
{
    "method": "login",
    "payload": {
        "username": "USERNAME",
        "password": "PASSWORD"
    }
}

{
    "name": "USERNAME",
    "key": "LOGIN_KEY",
    "expiry": "LOGIN_KEY_EXPIRY"
}
```

Login (with key):
```
{
    "method": "login",
    "payload": {
        "key": "KEY",
    }
}

{
    "name": "USERNAME",
    "key": "LOGIN_KEY",
    "expiry": "LOGIN_KEY_EXPIRY"
}
```
### Lobby
Create:
```
{
    "method": "createLobby",
    "payload": {
        "name": "LOBBY_NAME",
    }
}

{
    "name": "CREATED_NAME
}
```

Rename:
```
{
    "method": "renameLobby",
    "payload": {
        "old": "CURRENT_NAME",
        "new": "DESIRED_NAME"
    }
}

{
    "name": "NEW_NAME"
}
```

Join:
```
{
    "method": "joinLobby",
    "payload": {
        "lobby": "LOBBY_NAME",
    }
}

{
    "success" : "true"
}
```

Leave:
```
{
    "method": "leaveLobby",
    "payload": {
        "lobby": "LOBBY_NAME",
    }
}

{
    "success" : "true"
}

```

Message:
```
{
    "method": "lobbyMessage",
    "payload": {
        "lobby": "LOBBY_NAME",
        "message": "MESSAGE"
    }
}

{
    "lobby": "LOBBY_NAME",
    "from": "USER_NAME",
    "time": UNIX_TIME,
    "message": "MESSAGE"
}
```
