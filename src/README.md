## JSON Message Format

```
{
    "method": string,
    "payload": {
        "field 1": string,
        "field N": int
    }
}
```
### Login

Login (with credentials):
```
{
    "method": "login",
    "payload": {
        "username": string
    }
}

{
    "name": string
    "key": string,
    "expiry": string,
    "inGame": boolean
}
```

Login (with key):
```
{
    "method": "login",
    "payload": {
        "key": string,
    }
}

{
    "name": string
    "key": string,
    "expiry": string,
    "inGame": boolean
}
```
