@echo off
echo Iniciando servidor Angular con proxy...
cd frontend-examen
ng serve --proxy-config proxy.conf.json --port 4200
