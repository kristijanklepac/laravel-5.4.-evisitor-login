# laravel-5.4.-evisitor-login
Laravel Facade for private project - login through evisitor API (Croatian Tourist Board API)
#### created for my own app
#### use it as Facade 
#### in the routes or controller:
use Facades\ {
    App\Evisitor
};

Route::get('/', function () {
    return Evisitor::login();
});

