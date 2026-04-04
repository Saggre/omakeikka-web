<?php

namespace Deployer;

require 'recipe/common.php';

// -------------------------------------------------------------------------
// Config
// -------------------------------------------------------------------------

set('application', 'omakeikka-web');
$ghToken = getenv('GH_DEPLOY_TOKEN');
$ghRepo  = getenv('GITHUB_REPOSITORY');
set('repository', $ghToken
    ? 'https://oauth2:' . $ghToken . '@github.com/' . $ghRepo . '.git'
    : 'https://github.com/' . $ghRepo . '.git'
);
set('git_tty', false);
set('keep_releases', 5);
set('shared_files', ['.env']);
set('shared_dirs', ['web/app/uploads']);
set('writable_dirs', ['web/app/uploads']);
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0775');
set('composer_options', '--prefer-dist --no-dev --no-progress --optimize-autoloader');

// -------------------------------------------------------------------------
// Hosts
// -------------------------------------------------------------------------

host('staging')
    ->set('remote_user', getenv('DEPLOY_USER') ?: 'omakeikkafi')
    ->set('hostname', getenv('DEPLOY_HOST'))
    ->set('deploy_path', getenv('DEPLOY_PATH'))
    ->set('branch', 'staging');

host('production')
    ->set('remote_user', getenv('DEPLOY_USER') ?: 'omakeikkafi')
    ->set('hostname', getenv('DEPLOY_HOST'))
    ->set('deploy_path', getenv('DEPLOY_PATH'))
    ->set('branch', 'production');

// -------------------------------------------------------------------------
// Tasks
// -------------------------------------------------------------------------

task('deploy:theme:vendors', function () {
    run('cd {{release_path}}/web/app/themes/omakeikka-theme && {{bin/composer}} install --prefer-dist --no-dev --no-progress --optimize-autoloader --ignore-platform-req=ext-pdo');
});

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:theme:vendors',
    'deploy:publish',
]);

// Theme assets are built in CI - Node.js is not available on the server.
// Upload the compiled theme public/ directory after code is in place.
after('deploy:update_code', function () {
    upload('web/app/themes/omakeikka-theme/public/', '{{release_path}}/web/app/themes/omakeikka-theme/public/');
});

after('deploy:failed', 'deploy:unlock');
