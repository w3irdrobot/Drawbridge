<?php namespace Searsaw\Drawbridge\Commands;

use Illuminate\Console\Command;

class MigrationsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drawbridge:migrations';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export the Drawbridge migrations to the \'app/database/migrations\' directory.';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
        $app = app();
        $app['view']->addNamespace('drawbridge', substr(__DIR__, 0, -8) . 'views');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->info('Creating migration in app/database/migrations...');

        if ($this->createMigration())
        {
            $this->call('dump-autoload');
            $this->call('optimize');
            $this->info('Migration created! Enjoy.');
        }
        else
        {
            $this->error('Could not create the migration.  Make sure we have write permissions for this directory and the file doesn\'t already exist.');
        }
	}

    /**
     * Create migration for tables
     *
     * @return bool
     */
    public function createMigration()
    {
        $migration = $this->laravel->path . '/database/migrations/' . date('Y_m_d_His') . '_drawbridge_migrations_tables.php';
        $view = $this->laravel->make('view')->make('drawbridge::migration')->render();

        if (! file_exists($migration))
        {
            $fs = fopen($migration, 'x');
            if ($fs)
            {
                fwrite($fs, $view);
                fclose($fs);
                return true;
            }
        }

        return false;
    }

}
