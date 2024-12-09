1 - Após clonar o projeto rodar o seguinte comando no terminal: docker-compose up -d

2 - Rodar o seguinte comando para as migrations: docker-compose exec laravel_app php artisan migrate

2 - Após rodar o seguinte comando: docker-compose exec laravel_app php artisan serve --host=0.0.0.0

3 - Para acessar o container rodar o seguinte comando: docker exec -it laravel_app bash

4 - Para rodar os testes: php artisan test

Obs: inclui uma rotina de CI/CD para usar o GitHub Actions para rodar o sistema e fazer todos os testes.


