<p align="center"><a href="https://github.com/vichetmoeng/RestaurantWebApp" target="_blank" style="font-size: 72px">Restaurant Web App</a></p>

<p align="center">_____________________________________________________</p>

## About Project

This is a Laravel 6 project that have basic functionality of Restaurant. 
I created by follow a tutorial for learning Laravel perpose only.

## How to Setup and Use this project [Linux]


- Clone the project 
   > git clone https://github.com/vichetmoeng/RestaurantWebApp.git
- Go to project
    > cd RestaurantWebApp
- Open project with any code editor and modify .env file
    > cp .env.example .env
- .env file config database. For example:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=restaurantWebApp
    DB_USERNAME=root
    DB_PASSWORD=root
    ```
- Create new database in Mysql
    > login to mysql and type:
  > >create database restaurantWebApp
- Go back to project root directory and open terminal
    > php artisan migrate
- And to generate default admin user run:
    > php artisan db:seed
  - user: ```iamadmin```
  - email: ```iam@admin.com```
  - password: ```1337456789```
- To run project type:
    > php artisan serve
    - open that url and use that default admin user to login
### Now you can use this web app as you want. 
#Thanks
