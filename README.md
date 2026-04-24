# Laravel API - User CRUD

A clean Laravel API implementation with user CRUD functionality following clean architecture principles.

## Features

- **CRUD Operations**: Create, Read, Update, Delete users
- **Clean Architecture**: Separated Controllers, Services, and Requests
- **FormRequest Validation**: Comprehensive validation for all operations
- **Structured JSON Responses**: Consistent API response format
- **API Versioning**: Routes under `/api/v1/users`
- **Database Migrations & Seeders**: Proper database setup with test data
- **Clean & Scalable Code**: Following Laravel best practices

## Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB or any supported database

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   ```bash
   # Edit .env file with your database credentials
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_api
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## API Endpoints

### Users API (`/api/v1/users`)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/users` | Get all users |
| POST | `/api/v1/users` | Create a new user |
| GET | `/api/v1/users/{id}` | Get a specific user |
| PUT/PATCH | `/api/v1/users/{id}` | Update a user |
| DELETE | `/api/v1/users/{id}` | Delete a user |

## Request/Response Format

### Create User (POST)
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com"
}
```

### Success Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "created_at": "2024-04-15T17:30:00.000000Z",
        "updated_at": "2024-04-15T17:30:00.000000Z"
    },
    "message": "User created successfully"
}
```

### Error Response
```json
{
    "success": false,
    "message": "The name field is required."
}
```

## Testing

### Using curl

1. **Get all users**
   ```bash
   curl -X GET http://localhost:8000/api/v1/users
   ```

2. **Create a user**
   ```bash
   curl -X POST http://localhost:8000/api/v1/users \
   -H "Content-Type: application/json" \
   -d '{"name":"John Doe","email":"john.doe@example.com"}'
   ```

3. **Get a specific user**
   ```bash
   curl -X GET http://localhost:8000/api/v1/users/1
   ```

4. **Update a user**
   ```bash
   curl -X PUT http://localhost:8000/api/v1/users/1 \
   -H "Content-Type: application/json" \
   -d '{"name":"John Updated","email":"john.updated@example.com"}'
   ```

5. **Delete a user**
   ```bash
   curl -X DELETE http://localhost:8000/api/v1/users/1
   ```

### Using Postman/Insomnia

Import the following collection or manually create requests:

- **Base URL**: `http://localhost:8000/api/v1`
- **Headers**: `Content-Type: application/json`

## Validation Rules

### Store User
- `name`: Required, string, min 2 characters, max 255 characters
- `email`: Required, email, unique in users table, max 255 characters

### Update User
- `name`: Required, string, min 2 characters, max 255 characters
- `email`: Required, email, unique (except current user), max 255 characters

## Architecture

```
app/
 Controllers/Api/V1/
   UserController.php     # API endpoints handling
 Http/Requests/
   StoreUserRequest.php    # Create user validation
   UpdateUserRequest.php   # Update user validation
 Services/
   UserService.php         # Business logic
 Models/
   User.php               # Eloquent model
```

## License

This project is open-sourced software licensed under the MIT license.
