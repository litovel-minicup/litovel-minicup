<?php

namespace Deployer;

require 'recipe/common.php';
require 'recipe/rsync.php';

set('rsync', [
    'exclude' => [
        '.git',
        '.idea',
        'log',
        'temp',
        'utils',
        'vendor',
        'README.md',
        'minicup/config/config.local.neon',
        'www/media',
        'www/assets/.sass-cache',
        'www/assets/scss',
        'www/assets/vue',
        'www/webtemp',
        'node_modules',
    ],
    'exclude-file' => false,
    'include' => [
    ],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'vrzcE', // Recursive, with compress
    'options' => ['delete'],
    'timeout' => 60*3,
]);

// Project name
set('application', 'minicup.cz');

// Project repository
set('repository', 'git@github.com:litovel-minicup/litovel-minicup.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys 
set('shared_files', [
    'minicup/config/config.local.neon'
]);
set('shared_dirs', [
    'www/media'
]);

// Writable dirs by web server 
set('writable_dirs', [
    'log',
    'temp/cache',
    'www/webtemp',
    'www/media'
]);


// Hosts

host('minicup')
    ->stage('production')
    ->roles('app')
    ->set('deploy_path', '/var/www/html/{{application}}')
    ->set('rsync_src', __DIR__)
    ->set('rsync_dest', '{{release_path}}');

// Tasks

task('deploy:update_nginx', function () {
    run('cp {{current_path}}/conf/nginx/minicup.conf /etc/nginx/sites-available/');

    run('cp {{current_path}}/conf/nginx/common.conf /etc/nginx/');
    run('cp {{current_path}}/conf/nginx/php.conf /etc/nginx/');
    run('cp {{current_path}}/conf/nginx/nette.conf /etc/nginx/');
});

task('deploy:update_php-fpm', function () {
    run('cp {{current_path}}/conf/php-fpm/minicup.conf /etc/php/7.2/fpm/pool.d/');
});

desc('Deploy minicup from master!');
task('deploy', [
    'deploy:info',
    'deploy:webpack_build',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:migrate',

    'deploy:update_nginx',
    'deploy:update_php-fpm',
    'deploy:update_perms',

    'deploy:unlock',
    'cleanup',
    'success'
]);

task('deploy:webpack_build', function () {
    run('npm run build');
})->local();

task('deploy:migrate', function () {
    run('php {{current_path}}/www/index.php migrations:migrate --no-interaction');
});

task('reload:php-fpm', function () {
    run('service php7.2-fpm restart');
});

task('reload:nginx', function () {
    run('service nginx restart');
});

task('deploy:update_perms', function () {
    run('find . -type d -exec chmod +r {} \;');
});


desc('Deploy minicup from local!');
task('deploy_local', [
    'deploy:info',
    'deploy:webpack_build',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:migrate',

    'deploy:update_nginx',
    'deploy:update_php-fpm',
    'deploy:update_perms',

    'deploy:unlock',
    'cleanup',
    'success'
]);

after('rollback', 'deploy:update_nginx');
after('rollback', 'deploy:update_php-fpm');
after('rollback', 'reload:php-fpm');
after('rollback', 'reload:nginx');

after('deploy', 'reload:php-fpm');
after('deploy', 'reload:nginx');
after('deploy_local', 'reload:php-fpm');
after('deploy_local', 'reload:nginx');
// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
