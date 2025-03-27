# Real Estate Website

A modern real estate website built with Laravel and Vue.js, featuring property listings, interactive maps, and property comparison functionality.

## Features

- Interactive Google Maps integration with property markers
- Property clustering based on price ranges
- Advanced property filtering
- Property comparison tool with sharing capabilities
- Responsive design
- Email sharing functionality

## Requirements

- PHP >= 8.1
- Node.js >= 16
- Composer
- MySQL
- Google Maps API Key

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/real-estate.git
cd real-estate
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=real_estate
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Add your Google Maps API key to `.env`:
```
VITE_GOOGLE_MAPS_API_KEY=your_api_key
```

8. Run migrations and seeders:
```bash
php artisan migrate --seed
```

9. Build assets:
```bash
npm run dev
```

10. Start the development server:
```bash
php artisan serve
```

## Usage

1. Visit `http://localhost:8000` in your browser
2. Browse properties on the map or grid view
3. Use filters to find specific properties
4. Compare properties by adding them to the comparison panel
5. Share comparisons via email or social media

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.