<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Repositories\LoopRepository;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products or customers csv data from server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LoopRepository $loopRepository)
    {
        $type = $this->argument('type');
        $response = $loopRepository->importsData($type);
        $this->info($response);
        return Command::SUCCESS;
    }
}
