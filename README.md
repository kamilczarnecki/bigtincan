Running the application 

#Steps: 
- composer install
- cp .env.example .env
- php artisan key:generate
- ./vendon/bin/sail up -d
- ./vendor/bin/sail artisan migrate

# Available Routes: 
- GET {url}/api/files Get all files list
- GET {url}/api/files/{id} Get file id details
- PUT/PATCH {url}/api/files/{id} Update file 
- DELETE {url}/api/files/{id} Delete File
- GET {url}/files/download/{id} Downloads file through browser
