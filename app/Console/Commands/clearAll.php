<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class clearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all {--force} {--map}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clear all caches (view, cache, config, log)';
    private $disk;

    /**
     * Create a new command instance.
     * @param \Illuminate\Filesystem\Filesystem $disk
     * @return void
     */
    public function __construct(Filesystem $disk)
    {
        parent::__construct();
        $this->disk = $disk;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $isForce = (!empty($this->option('force'))) ? true : false;
        $hasMap = (!empty($this->option('map'))) ? true : false;
        if (!$isForce) {
            $excludedCaches = cache()->getMultiple([
                'notifications.owner.properties.count',
                'notifications.properties.rent.yearly.due.date.count',
                'notifications.applicants.count',
                'notifications.questions.count',
                'users.have_cities',
                'users.have_cities',
                'searches.results',
                'searches.canonicals',
                'properties.impressions',
            ]);

            $this->call('cache:clear');

            cache()->setMultiple($excludedCaches, 100000 * 60);
        } else {
            $this->call('cache:clear');
        }

        if ($hasMap) {
            $path = '/media/properties/maps';
            if (\Storage::exists($path)) {
                \Storage::deleteDirectory($path);
            }
        }
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        foreach ($this->disk->allFiles(storage_path('logs')) as $file) {
            $this->disk->delete($file);
        }
        $this->info('log files cleared!');

        foreach ($this->disk->allFiles(storage_path('debugbar')) as $file) {
            $this->disk->delete($file);
        }
        $this->info('debugbar files cleared!');
    }
}
