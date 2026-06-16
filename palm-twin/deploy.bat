@echo off
REM Happy deploy command for Windows — delegates to deploy.sh via Git Bash.
REM   deploy.bat                 (commit all, push, redeploy)
REM   deploy.bat "my message"    (custom commit message)

where bash >nul 2>nul
if %errorlevel%==0 (
  bash deploy.sh %*
) else (
  echo Bash not found. Install "Git for Windows" then run this again,
  echo or run:  bash deploy.sh   inside a Git Bash terminal.
  exit /b 1
)
