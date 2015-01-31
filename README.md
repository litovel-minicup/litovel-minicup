# Minicup-2015
Sport web application for handball tournament written in Nette


### Run requirements
1. set folders *temp/*, *log/* and *www/webtemp* as writable
2. create database and run sql command from last file in *utils/*
3. create file *minicup/config/config.local.neon* from *config.local.template.neon* in same folder and fill name of your DB, username and pass
4. install composer dependencies - $ php composer update from project root
5. if you want to develop (S)CSS, you have to set SCSS compiler to watching file *www/scss/index.scss* and compiling to *www/css/index.css* - recommended is scss ruby compiler
