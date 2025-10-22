# Smart Home API Documentation

## Authentication

All API endpoints require authentication using either:
1. JWT token in the Authorization header
2. API key in the query string

Example:
```
Authorization: Bearer <jwt_token>
```
or
```
GET /api/get_latest.php?api_key=<your_api_key>
```

## Endpoints

### User Management

#### Add User
- **URL:** `/api/add_user.php`
- **Method:** `POST`
- **Auth Required:** Yes (Admin only)
- **Data:**
```json
{
    "username": "string",
    "password": "string",
    "email": "string",
    "role": "string"
}
```
- **Success Response:** `{"status": "success", "message": "User created successfully"}`

### RFID Management

#### Check RFID
- **URL:** `/api/check_rfid.php`
- **Method:** `POST`
- **Auth Required:** Yes
- **Data:**
```json
{
    "rfid": "string"
}
```
- **Success Response:** `{"status": "success", "user": {...}}`

#### Delete RFID User
- **URL:** `/api/delete_rfid_user.php`
- **Method:** `POST`
- **Auth Required:** Yes (Admin only)
- **Data:**
```json
{
    "rfid": "string"
}
```
- **Success Response:** `{"status": "success", "message": "RFID user deleted"}`

### Sensor Data

#### Insert Sensor Data
- **URL:** `/api/insert_sensor.php`
- **Method:** `POST`
- **Auth Required:** Yes
- **Data:**
```json
{
    "device_id": "string",
    "sensor_type": "string",
    "value": "number"
}
```
- **Success Response:** `{"status": "success", "message": "Data inserted"}`

#### Get Latest Readings
- **URL:** `/api/get_latest.php`
- **Method:** `GET`
- **Auth Required:** Yes
- **Parameters:**
  - device_id (optional)
  - sensor_type (optional)
- **Success Response:** `{"status": "success", "data": [...]}`

#### Get History
- **URL:** `/api/get_history.php`
- **Method:** `GET`
- **Auth Required:** Yes
- **Parameters:**
  - device_id
  - sensor_type
  - start_date (optional)
  - end_date (optional)
- **Success Response:** `{"status": "success", "data": [...]}`

### Device Management

#### Register Device
- **URL:** `/api/register_device.php`
- **Method:** `POST`
- **Auth Required:** Yes (Admin only)
- **Data:**
```json
{
    "name": "string",
    "description": "string"
}
```
- **Success Response:** `{"status": "success", "api_key": "string"}`

## Error Responses

All endpoints may return the following error responses:

- 400 Bad Request: `{"status": "error", "message": "Invalid parameters"}`
- 401 Unauthorized: `{"status": "error", "message": "Authentication required"}`
- 403 Forbidden: `{"status": "error", "message": "Insufficient permissions"}`
- 500 Server Error: `{"status": "error", "message": "Internal server error"}`

## Rate Limiting

API requests are limited to:
- 100 requests per minute for authenticated users
- 1000 requests per minute for devices with API keys

## Data Formats

### DateTime
All dates should be in ISO 8601 format: `YYYY-MM-DD HH:mm:ss`

### Sensor Types
Valid sensor types:
- temperature
- humidity
- motion
- light
- pressure