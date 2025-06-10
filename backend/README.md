# Contest System Backend

Backend API for the Contest System built with PHP and SQL Server.

## Requirements

- PHP 8.1+
- SQL Server 2019+
- Composer
- Required PHP Extensions:
  - pdo_sqlsrv
  - sqlsrv
  - gd
  - mbstring

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/contest-system.git
   cd contest-system/backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   # Hoặc tạo file .env và điền các biến môi trường theo mẫu trong .env.example
   ```

4. Update environment variables in `.env`:
   ```
   DB_HOST=localhost
   DB_NAME=contest_db
   DB_USER=sa
   DB_PASS=YourStrong@Passw0rd
   ```

5. Run database migrations:
   ```bash
   php scripts/setup.php
   ```

## Development

Start the development server:
```bash
php -S localhost:8000
```

## API Documentation

The API documentation is available at `/docs/api/README.md`.

## Testing

Run tests:
```bash
composer test
```

## Directory Structure

```
backend/
├── api/                # API endpoints
│   ├── auth/          # Authentication endpoints
│   ├── contests/      # Contest endpoints
│   ├── users/         # User endpoints
│   └── votes/         # Voting endpoints
├── config/            # Configuration files
├── controllers/       # Business logic
├── middleware/        # Middleware functions
├── models/           # Database models
├── utils/            # Utility functions
└── scripts/          # Utility scripts
```

## Security

- All API endpoints are protected with JWT authentication
- Passwords are hashed using bcrypt
- CORS is configured to allow only specific origins
- Input validation and sanitization is implemented
- SQL injection prevention using prepared statements

## Error Handling

- Custom error handler for consistent error responses
- Detailed error logging
- Maintenance mode support
- Rate limiting for API endpoints

## Contributing

Please read [CONTRIBUTING.md](../CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](../LICENSE) file for details.