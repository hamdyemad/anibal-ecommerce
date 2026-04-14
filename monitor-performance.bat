@echo off
echo ========================================
echo   LOGIN PAGE PERFORMANCE MONITOR
echo ========================================
echo.
echo Monitoring: storage/logs/performance.log
echo.
echo Instructions:
echo 1. Open your browser to http://127.0.0.1:8000
echo 2. Watch this window for performance data
echo 3. Press Ctrl+C to stop monitoring
echo.
echo ========================================
echo.

powershell -Command "Get-Content storage\logs\performance.log -Wait -Tail 50"
