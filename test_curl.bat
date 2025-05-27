@echo off
cd /d "c:\Apache24\htdocs\mysite\Zoomwheels"
echo Testing registration POST request...
curl -X POST -d "username=testuser123&email=test@example.com&first_name=Test&last_name=User&password=password123&confirm_password=password123" http://localhost:8000/register
echo.
echo Test completed.
