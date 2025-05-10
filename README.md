# Hospital in Oborniki Website

Official website for the Hospital in Oborniki (Samodzielny Publiczny Zakład Opieki Zdrowotnej w Obornikach), developed using modern web technologies and best practices.

🌐 **[Visit the website (szpital.oborniki.info)](https://szpital.oborniki.info)**

## Technologies

### Backend
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3-fb70a9?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![Spatie](https://img.shields.io/badge/Spatie_Permission-6-orange?style=for-the-badge&logo=laravel&logoColor=white)](https://spatie.be/docs/laravel-permission)

### Frontend
[![React](https://img.shields.io/badge/React-19-61DAFB?style=for-the-badge&logo=react&logoColor=black)](https://react.dev)
[![Inertia](https://img.shields.io/badge/Inertia.js-2-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Tailwind](https://img.shields.io/badge/Tailwind-4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![shadcn/ui](https://img.shields.io/badge/shadcn/ui-0.7-000000?style=for-the-badge&logo=shadcnui&logoColor=white)](https://ui.shadcn.com)

## Prerequisites

- PHP 8.2 or higher
- Node.js 22 or higher
- Composer
- npm

### Installation

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Set up environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
composer run dev

# Build for production
npm run build
```

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Author

Bogusław Witek

---

<div align="center" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
    <span>Coded with</span>
    <a href="https://www.windsurf.io">
        <picture>
            <source media="(prefers-color-scheme: dark)" srcset="https://exafunction.github.io/public/brand/windsurf-black-wordmark.svg">
            <source media="(prefers-color-scheme: light)" srcset="https://exafunction.github.io/public/brand/windsurf-white-wordmark.svg">
            <img alt="Windsurf" src="https://exafunction.github.io/public/brand/windsurf-white-wordmark.svg" width="200" style="vertical-align: middle;">
        </picture>
    </a>
</div>
