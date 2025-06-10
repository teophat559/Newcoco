# Contest System Frontend

Frontend application for the Contest System built with React and Vite.

## Requirements

- Node.js 18+
- npm/yarn

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/contest-system.git
   cd contest-system/frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Create environment file:
   ```bash
   cp .env.example .env
   # Hoặc tạo file .env và điền các biến môi trường theo mẫu trong .env.example
   ```

4. Update environment variables in `.env`:
   ```
   VITE_API_URL=http://localhost:8000
   ```

## Development

Start the development server:
```bash
npm run dev
```

The application will be available at `http://localhost:3000`.

## Building for Production

Build the application:
```bash
npm run build
```

The built files will be in the `dist` directory.

## Testing

Run tests:
```bash
npm test
```

## Directory Structure

```
frontend/
├── public/             # Static files
│   ├── css/           # CSS files
│   ├── js/            # JavaScript files
│   ├── images/        # Image files
│   └── fonts/         # Font files
├── src/               # Source files
│   ├── components/    # React components
│   ├── pages/         # Page components
│   ├── utils/         # Utility functions
│   └── api/           # API integration
└── dist/              # Build output
```

## Features

- Modern React with hooks and functional components
- TypeScript for type safety
- React Router for navigation
- React Query for data fetching
- Formik and Yup for form handling
- Tailwind CSS for styling
- Headless UI components
- Responsive design
- Dark mode support
- Internationalization ready

## Contributing

Please read [CONTRIBUTING.md](../CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](../LICENSE) file for details.