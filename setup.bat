@echo off
cd /d "%~dp0"

echo === SafeZone Setup ===
echo Folder proyek: %CD%
echo.

if not exist "vendor\" (
    echo Install dependency PHP (composer)...
    composer install --ignore-platform-reqs
    if errorlevel 1 (
        echo GAGAL: composer install. Pastikan Composer terinstall.
        pause
        exit /b 1
    )
)

if not exist "node_modules\" (
    echo Install dependency JS (npm)...
    call npm install
)

if not exist ".env" (
    echo Membuat file .env...
    copy .env.example .env
)

echo Generate APP_KEY...
php artisan key:generate

echo.
echo Jalankan migration + seed...
php artisan migrate --seed

echo.
echo Build assets frontend...
call npm run build

echo.
echo === Selesai! ===
echo Login: user@safezone.com / password
echo Admin: admin@safezone.com / password
pause
