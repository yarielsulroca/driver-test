Write-Host "Iniciando servidor Angular con proxy..." -ForegroundColor Green
Set-Location frontend-examen
ng serve --proxy-config proxy.conf.json --port 4200
