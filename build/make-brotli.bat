@echo off
SET SCRIPTDIR=%~dp0
SET OUTPUTDIR=%SCRIPTDIR%..\deps
SET LOGFILE=%SCRIPTDIR%\build.log

REM --- Define absolute path
for %%I in ("%OUTPUTDIR%") do set OUTPUTDIR=%%~fI

REM --- Prerequisite Check ---
ECHO Checking for the 'cmake' tool...
WHERE cmake > NUL 2> NUL

IF %ERRORLEVEL% NEQ 0 (
    ECHO.
    ECHO [FATAL ERROR] The 'cmake' tool was not found in your PATH.
    ECHO Please install it before committing.
    ECHO See https://cmake.org/download/
    ECHO Commit aborted.
    EXIT /B %ERRORLEVEL%
)

ECHO Checking for the 'cl' tool...
for /f "usebackq tokens=*" %%i in (`"%ProgramFiles(x86)%\Microsoft Visual Studio\Installer\vswhere.exe" -latest -products * -requires Microsoft.Component.MSBuild -property installationPath`) do (
    call "%%i\Common7\Tools\VsDevCmd.bat" -arch=x64
)

REM -- Runtime definition
SET FONTFINDERWOFF2=%Temp%\font-finder-woff2
SET CMAKEVER=3.15
SET CONFIG=Release

REM -- Removing woff2 folder
del /S /Q %FONTFINDERWOFF2% > NUL 2> NUL
rmdir /S /Q %FONTFINDERWOFF2%

REM -- Clone woff2 main repository
echo Cloning woff2...
git clone --recursive https://github.com/google/woff2.git %FONTFINDERWOFF2% > "%LOGFILE%" 2>&1

REM  - Build brotli
echo Building brotli...

cd %FONTFINDERWOFF2%\brotli
if not exist out mkdir out
cmake -S . -B out -DCMAKE_POLICY_VERSION_MINIMUM=%CMAKEVER% >> "%LOGFILE%" 2>&1
cmake --build out --config %CONFIG% >> "%LOGFILE%" 2>&1

if %ERRORLEVEL% neq 0 (
    echo [FATAL ERROR] Brotli build failed!
    exit /b %ERRORLEVEL%
)

REM - Build woff2
echo Building woff2...
cd %FONTFINDERWOFF2%
if not exist out mkdir out
cmake -S . -B out -DCMAKE_POLICY_VERSION_MINIMUM=%CMAKEVER% -DCMAKE_WINDOWS_EXPORT_ALL_SYMBOLS=TRUE -DBUILD_SHARED_LIBS=FALSE ^
    -DBROTLIDEC_INCLUDE_DIRS="%CD%\brotli\c\include" -DBROTLIDEC_LIBRARIES="%CD%\brotli\out\%CONFIG%\brotlidec.lib" ^
    -DBROTLIENC_INCLUDE_DIRS="%CD%\brotli\c\include" -DBROTLIENC_LIBRARIES="%CD%\brotli\out\%CONFIG%\brotlienc.lib"  >> "%LOGFILE%" 2>&1

if %ERRORLEVEL% neq 0 (
    echo WOFF2 CMake configure failed!
    exit /b %ERRORLEVEL%
)

cmake --build out --config %CONFIG% >> "%LOGFILE%" 2>&1

if %ERRORLEVEL% neq 0 (
    echo WOFF2 build failed!
    exit /b %ERRORLEVEL%
)

REM -- Create deps
if not exist "%OUTPUTDIR%" mkdir "%OUTPUTDIR%"

REM -- Copy
:COPY_RETRY
copy /Y "%FONTFINDERWOFF2%\out\%CONFIG%\woff2_decompress.exe" "%OUTPUTDIR%" >> "%LOGFILE%" 2>&1
if errorlevel 1 (
    echo Copy failed, retrying in 1 second...
    timeout /t 1 > nul
    goto COPY_RETRY
)

echo Build complete, cleaning

REM -- Clean: removing woff2 folder
del /S /Q %FONTFINDERWOFF2% > NUL 2> NUL
:CLEAN_RETRY
REM -- TODO LIMIT CLEAN RETRY 10 TIMES
rmdir /S /Q %FONTFINDERWOFF2% >> "%LOGFILE%" 2>&1
if exist "%FONTFINDERWOFF2%" (
    timeout /t 1 > nul
    goto CLEAN_RETRY
)