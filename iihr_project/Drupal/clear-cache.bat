@echo off
REM ============================================================
REM  IIHR — Drupal Cache Rebuild Script
REM
REM  Theme and module files are live-mounted via Docker volumes,
REM  so no file copying is needed. Just rebuild Drupal's cache.
REM
REM  Run this from: C:\Users\softsuave\Desktop\IICR-Pro\iihr_project
REM ============================================================

echo [1/1] Rebuilding Drupal cache...
docker exec drupal_app ./vendor/bin/drush cr

echo.
echo Done! Open http://localhost:8080
echo.
echo TIP: After reinstalling iihr_module (to create content types), run:
echo   docker exec drupal_app ./vendor/bin/drush pmu iihr_module -y
echo   docker exec drupal_app ./vendor/bin/drush en iihr_module -y
echo   docker exec drupal_app ./vendor/bin/drush cr
