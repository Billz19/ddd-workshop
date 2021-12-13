<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Library\ArangoDb\ArangoDbInitializerCollection;

class InitArangoDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        init:arangodb
        {--db= : name of the database}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'initializing arango database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private ArangoDbInitializerCollection $dbInitializers,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $dbName = $this->option('db') ?? env('ARANGO_DATABASE', '_system');
            foreach ($this->dbInitializers->getInitializers() as $initializer) {
                $initializer->init($dbName);
            }
            $this->info('Database initialization job was successful!');
        }
        catch (\Exception $e) {
            $this->error('Database initialization job was failed!');
        }
    }
}
