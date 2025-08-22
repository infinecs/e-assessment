<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneExpiredAssessments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessments:prune-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired assessments from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Pruning expired assessments...');
        $count = \App\Models\AssessmentEvent::where('end_date', '<', now())->delete();
        $this->info("Pruned {$count} expired assessments.");
    }
}
