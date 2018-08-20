# Q-Blog
A very simple Blog with Admin Panel built using Laravel5.6 && VueJs2.5 - Quasar0.17


## Installation
Clone the repository.

```
git clone https://github.com/abdelaziz321/Q-Blog
```

change the directory into Q-Blog folder.

```
cd Q-Blog
```

##### First we will setup the laravel App

1- change the directory into the `backend` folder.

```
cd backend
```


2- install the dependencies by running Composer's install command.

```
composer install
```

3- create your environment file.

```
cp .env.example .env
```

4- Now generate the key that will be used to sign your tokens.

```
php artisan jwt:secret
```


5- make a symbolic link from `public/storage` to `storage/app/public`

```
php artisan storage:link
```


6- create a database named `Q-Blog` or whatever.

7- edit `.env` file with appropriate credential for your database server - these parameter(`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

8- migrate your database & Seed it with records.

```
php artisan migrate --seed
```


9- generate the application key.

```
php artisan key:generate
```



10- Run the server.

```
php artisan serve --port=8000
```
-- make sure the server is running in port `8000` or you have to change the `baseURL` var inside the `admin-panel` and the `public site`.

_____

at this point and if there is no errors, we installed our `API` successfully.

##### Now we will install the admin panel
go to the `admin-panel` folder.

```
cd ../admin-panel
```
install our dependencies.
```
yarn
```
Now run the server using
```
yarn serve
```

##### at the end we will install the public site
go to the `blog` folder.

```
cd ../blog
```
install our dependencies.
```
yarn
```
now we are done run this command to start the dev server
```
quasar dev
```
happy coding :)



## Screenshots

![dashboard](/.dev/screenshots/dashboard.png)

![user](/.dev/screenshots/user.png)

![users](/.dev/screenshots/users.png)

![blog](/.dev/screenshots/blog.jpg)

![single](/.dev/screenshots/single.png)




## Ask a question?

If you have any question, contact me via my email:
> abdelazizmahmoud321@gmail.com
