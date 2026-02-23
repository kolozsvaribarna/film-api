# Film API

A REST API for managing films, directors, actors, genres, and studios.

---

## Base URL

```
http://<your-host>/
```

---

## Response Format

All responses follow this structure:

```json
{
  "code": 200,
  "data": { ... }
}
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| `200` | OK – Request succeeded |
| `201` | Created – Resource successfully created |
| `202` | Accepted – Resource successfully updated |
| `204` | No Content – Resource successfully deleted |
| `400` | Bad Request – Invalid route or request data |
| `404` | Not Found – Resource does not exist |

---

## Resources

The API exposes five resources, each supporting the same set of endpoints:

- `/films`
- `/directors`
- `/actors`
- `/genres`
- `/studios`

---

## Endpoints

### Get all resources

```
GET /{resource}
```

Returns a list of all records for the given resource.

**Response `200`**
```json
{
  "code": 200,
  "data": [
    { "id": 1, ... },
    { "id": 2, ... }
  ]
}
```

---

### Get a resource by ID

```
GET /{resource}/{id}
```

Returns a single record matching the given ID.

**Response `200`**
```json
{
  "code": 200,
  "data": { "id": 1, ... }
}
```

**Response `404`** – if the ID does not exist
```json
{
  "code": 404,
  "data": []
}
```

---

### Create a resource

```
POST /{resource}
```

Creates a new record. Send the fields as a JSON body.

**Request Body**
```json
{
  "field_name": "value",
  ...
}
```

**Response `201`**
```json
{
  "code": 201,
  "data": { "id": 42 }
}
```

**Response `400`** – if creation fails
```json
{
  "code": 400,
  "data": { "error": "Bad request" }
}
```

---

### Update a resource

```
PUT /{resource}/{id}
```

Updates an existing record. Send only the fields you want to change as a JSON body.

**Request Body**
```json
{
  "field_name": "new_value",
  ...
}
```

**Response `202`**
```json
{
  "code": 202,
  "data": []
}
```

**Response `404`** – if the ID does not exist
```json
{
  "code": 404,
  "data": []
}
```

---

### Delete a resource

```
DELETE /{resource}/{id}
```

Deletes the record with the given ID.

**Response `204`**
```json
{
  "code": 204,
  "data": []
}
```

---

## Allowed Routes Summary

| Method | Route |
|--------|-------|
| `GET` | `/{resource}` |
| `GET` | `/{resource}/{id}` |
| `POST` | `/{resource}` |
| `PUT` | `/{resource}/{id}` |
| `DELETE` | `/{resource}/{id}` |

Where `{resource}` is one of: `films`, `directors`, `actors`, `genres`, `studios`.

Any other route returns:
```json
{
  "code": 400,
  "data": { "error": "Route not allowed" }
}
```
