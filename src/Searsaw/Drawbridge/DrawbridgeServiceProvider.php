<?php namespace Searsaw\Drawbridge;

use Illuminate\Support\ServiceProvider;
use Searsaw\Drawbridge\Commands\MigrationsCommand;

class DrawbridgeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('searsaw/drawbridge');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerCommands();
	}

    /**
     * Register the commands
     */
    private function registerCommands()
    {
        $this->app['commands.drawbridge.migrations'] = $this->app->share(function($app)
        {
            return new MigrationsCommand($app);
        });

        $this->commands(
            [
            'commands.drawbridge.migrations'
            ]
        );
    }

}