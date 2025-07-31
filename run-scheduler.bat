@echo off
cd /d "C:\Users\USER\Desktop\System\IOT\rainwater"

:loop
php artisan schedule:run
timeout /t 60 /nobreak
goto loop 