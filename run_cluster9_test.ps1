Start-Process -FilePath "H:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=H:\xampp\mysql\bin\my.ini" -WindowStyle Hidden
Start-Sleep -Seconds 3
php artisan test
Stop-Process -Name mysqld -Force -ErrorAction SilentlyContinue
