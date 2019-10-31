
#Dependencies (in case you dont have )
Install composer then test with composer -V (capital)

#Steps

cd to the project directory and run:

Step1) npm install

Step 2) composer install

Step 3) Rename .env.example to .env

Step 4) php artisan key:generate

#Database steps (with phpmyadmin)
Step 5) Open task manager

Step 6)End task "mysql"

Step 7)Open xampp

Step 8)Start apache and mysql

Step 9)go to localhost/phpmyadmin

Step 10)create a database "laravel_vms"

Step 11)go to folder "" then vendor then .env  then add database name

Step 12)then go to app then AppServiceProvider then AppServiceProvider.php then add 2 things there (ask me)

Step 13)go to phpmyadmin delete tables if already exist

Step 14)then go command prompt open file directory run

Step 15)php artisan migrate

database will be created


after run this:
Step 16)npm run build -- to build for production

Step 17)npm run watch -- this builds everytime you change anything in the files to built

#Open a new command prompt and cd to project directory then run

Step 18) php artisan serve -- this will start a local development server at port 8000 (you can specify host and port of your choice if you like)

You will be connected then enter into web browser : localhost:8000 or the url provided into the command prompt
"#vms"

#Commit and push to the branch


Step 1) Locate the file on windows, right click on Git GUI on the file directory of the project

Step 2) Once in GITGUI click Stage changed

Step 3) Click on Continue

Step 4) Add comment to commit changes you are exactly doing

Step 5) Click on commit

Step 6) Then arbitrary action add URL link GitHub  ("https://github.com/maximehkz/vms.git")

Step 7) Then push to commit to the branch on Github














REMOVE FROM classes/user - line 98


self::update(array(
							'last_login' => date("Y-m-d h:i:a")
						),$this->data()->user_id);
"# ciscovms" 
