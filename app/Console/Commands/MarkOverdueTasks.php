<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\TaskStatus;

class MarkOverdueTasks extends Command
{
    protected $signature = 'tasks:mark-overdue';
    protected $description = 'Mark tasks as overdue if due date is passed';

    public function handle()
    {
        $overdueStatus = TaskStatus::where('name', 'Overdue')->first();

        if (!$overdueStatus) {
            $this->error('Overdue status not found');
            return;
        }

        Task::whereDate('due_date', '<', now())
            ->whereNull('actual_due_date')
            ->where('status_id', '!=', $overdueStatus->id)
            ->update([
                'status_id' => $overdueStatus->id
            ]);

        $this->info('Overdue tasks updated successfully');
    }
}
