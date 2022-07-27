<p align="center"><a href="https://laravel.com" target="_blank">
<img src="https://luglockers.com/static/media/logo.1ccb7fb6.png" width="400"></a></p>

## Docker

To run this project with a docker you need to

- Run  ``` docker-compose up ``` command in the root folder
- Add ``` 127.0.0.1 luglockers.loc ``` string in your local hosts file
- Rename ```.env.example``` file to ```.env``` inside your project root and fill the database information.
    ``` 
    DB_HOST=database
    DB_PORT=3306
    DB_DATABASE=luglockers
    DB_USERNAME=root
    DB_PASSWORD=password
     ```
Then the site will be available by the address - ``` http://luglockers.loc:85 ```

phpMyAdmin will be available by the address - ``` localhost:8181 ```

### To connect to the database outside a docker container you need to use these variables

- host: **localhost**
- username: **root**
- password: **password**
- database: **luglockers**
- port: **3311**

