@echo off

taskkill /F /IM php.exe >nul 2>&1

cd /d C:\wamp64\www\saaskit

start /min php artisan sms:process
