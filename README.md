Drawbridge
===========
Roles and permissions for Laravel 4.

##Installation

Add `searsaw/drawbridge` as a requirement to your `composer.json`.

```json
{
  "require": {
    "searsaw/drawbridge": "dev-master"
  }
}
```

Update your application packages with `composer update` or install them with `composer install`.  Then add the service provider to `providers` array in the `app/config/app.php` file.

```php
'providers' => array(
    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    'Illuminate\Workbench\WorkbenchServiceProvider',
    'Searsaw\Drawbridge\DrawbridgeServiceProvider'
),
```

##Getting Started

First, we need to export the migrations into the `migrations` directory so the tables for Drawbridge can be put into the database. Run the `php artisan drawbridge:migrations` command to export them.  Then migrate the database with `php artisan migrate`.

For the roles and permissions to work, a Role model, a Permission model, and a User model need to exist.  Luckily, Drawbridge ships with three models you can easily extend to add the functionality to your models.  In your `app/models` directory, create the following two models.

```php
use Searsaw\Drawbridge\Models\BridgeRole;

class Role extends BridgeRole {}
```

```php
use Searsaw\Drawbridge\Models\BridgePermission;

class Permission extends BridgePermission {}
```

There is also a BridgeUser model included in the package to give the functionality for adding roles to your User model.  The below code adds the BridgeUser model functionality to the default User model that comes with Laravel.

```php
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Searsaw\Drawbridge\Models\BridgeUser;

class User extends BridgeUser implements UserInterface, RemindableInterface {

...
```

All three models extend Magniloquent, a model validation package for Laravel models.  Magniloquent extends Eloquent, giving your models all the functionality of Eloquent with the added benefit of model validation.  For more information on Magniloquent, [visit its Github page](https://github.com/philipbrown/magniloquent).

## Adding Roles and Permissions

Roles are groups of permissions.  Users can have different roles, giving them extended abilities depending on the roles they have.

To add roles to a user, use the `addRole` method on a user object.  This method can take a role ID, a role name, or a role object.  If the role object has not yet been saved to the database, it will be saved before being attached to the user.  This method can also take an array of any combination of the previous three.

To add permissions to a role, use the `addPermission` method on a role object.  This method works exactly as the `addRole` method mentioned above.  This makes adding permissions as easy as possible.

```php
$user = User::find(1);

$admin = new Role;
$admin->name = 'admin';
$admin->display_name = 'Admin';
$admin->save();

$author = new Role;
$author->name = 'author';
$author->display_name = 'Author';

$edit = new Permission;
$edit->name = 'can_edit';
$edit->display_name = 'Can Edit';
$edit->save();

$view = new Permission;
$view->name = 'can_view';
$view->display_name = 'Can View';
$view->save();

$user->addRole([$admin, $author]);

$admin->addPermission([$edit->id, 'can_view']);
```

##Checking for roles or permissions

To check if a user has a certain role, use the `hasRole` method on a user object.  It can take an ID, a name, or a Role object itself.  It returns `true` if the user has the role or `false` if the user does not.

To check if a role has a certain permission, use the `hasPermission` method on a role object.  It can take an ID, a name, or a Permission object itself.  It returns `true` if the role has the permission or `false` if the role does not.

You can also check to see if a user has a particular permission by using the the `hasPermission` method on a user object.  It can take an ID, a name, or a Permission object itself.  It returns `true` if the user has the permission or `false` if the user does not.

```php
$user = User::find(1);
$admin = Role::find(2);
$edit = Permission::find(1);

$user->hasRole('admin');
$user->hasRole($admin);
$user->hasRole(2);

$admin->hasPermission('can_edit');
$admin->hasPermission($edit);
$admin->hasPermission(1);

$user->hasPermission('can_edit');
$user->hasPermission($edit);
$user->hasPermission(1);
```

##License

The MIT License (MIT)

Copyright (c) 2013 Alex Sears

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.