# Contest System

A modern web application for managing and participating in contests.

## Project Structure

```
project/
├── frontend/                 # Frontend code
│   ├── public/              # Static files
│   ├── src/                # Source files
│   └── dist/               # Build output
│
├── backend/                 # Backend code
│   ├── api/                # API endpoints
│   ├── config/            # Configuration files
│   ├── models/            # Database models
│   ├── controllers/       # Business logic
│   └── middleware/        # Middleware functions
│
├── database/               # Database files
├── tests/                 # Test files
├── docs/                  # Documentation
└── scripts/              # Utility scripts
```

## Prerequisites

- PHP 8.1+
- SQL Server 2019+
- Node.js 18+
- Composer
- npm/yarn

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/contest-system.git
   cd contest-system
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   cd frontend
   npm install
   ```

4. Copy environment file:
   ```bash
   cp .env.example .env
   ```

5. Update environment variables in `.env`:
   ```
   DB_HOST=localhost
   DB_NAME=contest_db
   DB_USER=sa
   DB_PASS=YourStrong@Passw0rd
   ```

6. Run database migrations:
   ```bash
   php scripts/setup.php
   ```

7. Build frontend:
   ```bash
   cd frontend
   npm run build
   ```

## Development

1. Start backend server:
   ```bash
   php -S localhost:8000
   ```

2. Start frontend development server:
   ```bash
   cd frontend
   npm run dev
   ```

## Testing

Run tests:
```bash
php tests/test_flow.php
```

## Deployment

1. Deploy backend:
   - Upload backend files to your server
   - Configure web server (Apache/Nginx)
   - Set up SSL certificate

2. Deploy frontend:
   - Build frontend: `npm run build`
   - Deploy to Netlify/Vercel/etc.

## Documentation

- [API Documentation](docs/api/README.md)
- [Setup Guide](docs/setup/README.md)
- [Deployment Guide](docs/deployment/README.md)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.