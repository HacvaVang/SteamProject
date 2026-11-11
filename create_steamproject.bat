@echo off
setlocal EnableExtensions

REM ============================================================
REM SteamProject - Create project directories and .gitkeep files
REM Run this script from CMD or by double-clicking it.
REM The SteamProject folder will be created beside this .bat file.
REM ============================================================

set "ROOT=%~dp0SteamProject"

echo Creating SteamProject structure...
if not exist "%ROOT%" mkdir "%ROOT%"

REM Application structure
for %%D in (
    "app\Controllers"
    "app\Controllers\Admin"
    "app\Models"
    "app\Views"
    "app\Views\layouts"
    "app\Views\home"
    "app\Views\auth"
    "app\Views\user"
    "app\Views\products"
    "app\Views\cart"
    "app\Views\orders"
    "app\Views\news"
    "app\Views\about"
    "app\Views\contact"
    "app\Views\faq"
    "app\Views\admin"
    "app\Views\admin\layouts"
    "app\Views\admin\dashboard"
    "app\Views\admin\users"
    "app\Views\admin\products"
    "app\Views\admin\orders"
    "app\Views\admin\news"
    "app\Views\admin\comments"
    "app\Views\admin\contacts"
    "app\Views\admin\faqs"
    "app\Views\admin\settings"
    "app\Core"
    "app\Middleware"
    "app\Helpers"
    "config"
    "database"
    "public"
    "public\html"
    "public\assets"
    "public\assets\css"
    "public\assets\js"
    "public\assets\images"
    "public\assets\images\logo"
    "public\assets\images\banners"
    "public\assets\images\products"
    "public\assets\images\news"
    "public\assets\images\avatars"
    "public\assets\libraries"
    "public\assets\libraries\bootstrap"
    "public\assets\libraries\jquery"
    "public\assets\libraries\tailadmin"
    "public\uploads"
    "public\uploads\products"
    "public\uploads\news"
    "public\uploads\avatars"
    "routes"
    "storage"
    "storage\logs"
) do (
    if not exist "%ROOT%\%%~D" mkdir "%ROOT%\%%~D"
)

REM Create a .gitkeep in the project root and every subdirectory.
type nul > "%ROOT%\.gitkeep"
for /d /r "%ROOT%" %%D in (*) do (
    type nul > "%%D\.gitkeep"
)

echo.
echo SteamProject directory structure created successfully.
echo .gitkeep files added to the project root and all directories.
echo Location: "%ROOT%"
echo.
pause
endlocal
