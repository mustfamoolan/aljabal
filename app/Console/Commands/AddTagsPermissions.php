<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class AddTagsPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add tags permissions to the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('إضافة صلاحيات التاغات...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'tags.view',
            'tags.create',
            'tags.update',
            'tags.delete',
        ];

        foreach ($permissions as $permission) {
            $created = Permission::firstOrCreate(['name' => $permission]);
            if ($created->wasRecentlyCreated) {
                $this->info("تم إنشاء الصلاحية: {$permission}");
            } else {
                $this->line("الصلاحية موجودة بالفعل: {$permission}");
            }
        }

        $this->info('تم إضافة صلاحيات التاغات بنجاح!');

        return Command::SUCCESS;
    }
}
