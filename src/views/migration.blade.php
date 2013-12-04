{{ '<?php' }}

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DrawbridgeMigrationsTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('roles', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('users_roles', function(Blueprint $table)
        {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::create('roles_permissions', function(Blueprint $table)
        {
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles_permissions', function(Blueprint $table)
        {
            $table->dropForeign('roles_permissions_role_id_foreign');
            $table->dropForeign('roles_permissions_permission_id_foreign');
        });

        Schema::table('users_roles', function(Blueprint $table)
        {
            $table->dropForeign('users_roles_user_id_foreign');
            $table->dropForeign('users_roles_role_id_foreign');
        });

        Schema::drop('permissions');
        Schema::drop('roles');
        Schema::drop('users_roles');
        Schema::drop('roles_permissions');
    }

}
