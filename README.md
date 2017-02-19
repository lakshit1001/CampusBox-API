# CampusBox Api

## Valid Routes

- [token](#token)
- [events](#events)
- [reports](#reports)

## Usage

Various methods that are supported
- get - lists all the items.
- get/id - lists particular.
- patch/{{id}} - edit partially.
- put/{{id}} - edit completely(all the fields to be supplied or defaults are taken).
- delete/{{id}} - delets the item.

Now you can access the api at [https://192.168.50.52/todos](https://192.168.50.52/todos)

<a name="token"></a>
### Get a token

```
$ curl "https://192.168.50.52/token" \
    --request POST \
    --include \
    --insecure \
    --header "Content-Type: application/json" \
    --data '["todo.all"]' \
    --user test:test

HTTP/1.1 201 Created
Content-Type: application/json

{
    "status": "ok",
    "token": "XXXXXXXXXX"
```

<a name="events"></a>
### Create a new event

```
$ curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H "Cache-Control: no-cache" -d '{
        "created_by_id": "3",
        "college_id": "1",
        "type": "cultural",
        "price":200,
        "title": "ABC",
        "description": "asdf",
        "venue":"asdf",
        "inter":"0"
}' "http://localhost/app/public/events"
```

### Get an existing event

```
$ curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H "Cache-Control: no-cache" "http://localhost/app/public/events/1"
```

### Update part of an existing event

```
$ curl -X PATCH -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H "If-Unmodified-Since: Sat, 16 Apr 2016 10:27:16 GMT" -H "Cache-Control: no-cache" -d '{
        "created_by_id": "3",
        "college_id": "1",
        "type": "cultural",
        "price":200,
        "title": "ABCDE",
        "description": "asdf",
        "venue":"asdf",
        "inter":"0"
}' "http://localhost/app/public/events/9"
```

### Fully update an existing event

```
$ curl -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H "If-Unmodified-Since: 2017-02-12 08:11:39.000000" -H "Cache-Control: no-cache" -d '{
        "created_by_id": "3",
        "college_id": "1",
        "type": "cultural",
        "price":200,
        "title": "ABCDE",
        "description": "asdff",
        "venue":"asdf",
        "inter":"0"
}' "http://localhost/app/public/events/9"
```

<a name="reports"></a>
### Update a part of an existing report

```
$ curl -X PATCH -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H "If-Unmodified-Since: 2017-02-12 14:44:22.000000" -H "Cache-Control: no-cache" -H -d '{
        "reported_by_id": 2,
        "type": "lost and not found",
        "type_id": 1,
        "reason": "no reason",
        "reported": 1
}' "http://localhost/app/public/reports/1"
```

### Fully update an existing report
(todo)timestamp field to be renamed

```
$ curl -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H "If-Unmodified-Since: 2017-02-12 14:49:22.000000" -H "Cache-Control: no-cache" -d '{
        "type": "lost and not found",
        "type_id": 1,
        "reason": "no reason",
        "reported": 1
}' "http://localhost/app/public/reports/1"
```

### Delete a report

```
$ curl -X DELETE -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN>" -H -H "Cache-Control: no-cache" "http://localhost/app/public/reports/1"
```
