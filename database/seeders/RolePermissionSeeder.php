<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // DEFINE ALL PERMISSIONS
        // ============================================================
        $permissions = [
            // System
            'system-settings.view',
            'system-settings.create',
            'system-settings.update',
            'system-settings.delete',

            // Journal Management
            'journal.view',
            'journal.create',
            'journal.update',
            'journal.delete',

            // User & Role Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.assign',

            // Submission
            'submission.create',
            'submission.view',
            'submission.update',
            'submission.delete',
            'submission.submit',
            'submission.withdraw',

            // Screening
            'screening.view',
            'screening.decide',

            // Reviewer Assignment
            'reviewer.assign',
            'reviewer.view',

            // Peer Review
            'review.accept',
            'review.decline',
            'review.submit',
            'review.view',

            // Editorial Decision
            'decision.make',
            'decision.view',

            // Production
            'copyediting.manage',
            'layout.manage',
            'proofreading.manage',

            // Publication
            'publication.approve',
            'volume.manage',
            'issue.manage',
            'doi.manage',

            // Finance
            'invoice.view',
            'invoice.create',
            'invoice.update',
            'payment.verify',
            'payment.reject',
            'waiver.apply',
            'waiver.approve',
            'receipt.view',
            'receipt.generate',
            'refund.create',
            'refund.approve',

            // CMS
            'cms.view',
            'cms.create',
            'cms.update',
            'cms.delete',
            'cms.publish',

            // Reports
            'reports.view',
            'reports.export',

            // Audit
            'audit.view',

            // Notifications
            'notifications.configure',

            // API
            'api.tokens.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ============================================================
        // DEFINE ROLES WITH PERMISSIONS
        // ============================================================
        $rolePermissions = [
            'super-admin' => Permission::all(),
            'system-admin' => Permission::all(),

            'journal-manager' => [
                'journal.view',
                'journal.update',
                'users.view',
                'users.create',
                'users.update',
                'submission.view',
                'screening.view',
                'screening.decide',
                'reviewer.assign',
                'reviewer.view',
                'decision.view',
                'copyediting.manage',
                'layout.manage',
                'proofreading.manage',
                'publication.approve',
                'volume.manage',
                'issue.manage',
                'doi.manage',
                'invoice.view',
                'invoice.create',
                'invoice.update',
                'payment.verify',
                'waiver.apply',
                'waiver.approve',
                'receipt.view',
                'cms.view',
                'cms.create',
                'cms.update',
                'cms.delete',
                'cms.publish',
                'reports.view',
                'reports.export',
                'audit.view',
                'notifications.configure',
                'api.tokens.manage',
            ],

            'managing-editor' => [
                'submission.view',
                'screening.view',
                'screening.decide',
                'reviewer.assign',
                'reviewer.view',
                'decision.make',
                'decision.view',
                'publication.approve',
                'volume.manage',
                'issue.manage',
                'cms.view',
                'cms.update',
                'reports.view',
            ],

            'section-editor' => [
                'submission.view',
                'screening.view',
                'screening.decide',
                'reviewer.assign',
                'reviewer.view',
                'decision.make',
                'decision.view',
            ],

            'reviewer' => [
                'review.accept',
                'review.decline',
                'review.submit',
                'review.view',
            ],

            'copy-editor' => [
                'copyediting.manage',
                'submission.view',
            ],

            'layout-editor' => [
                'layout.manage',
                'submission.view',
            ],

            'proofreader' => [
                'proofreading.manage',
                'submission.view',
            ],

            'publisher' => [
                'publication.approve',
                'volume.manage',
                'issue.manage',
                'doi.manage',
                'cms.view',
                'cms.update',
                'reports.view',
            ],

            'finance' => [
                'invoice.view',
                'invoice.create',
                'invoice.update',
                'payment.verify',
                'payment.reject',
                'waiver.apply',
                'receipt.view',
                'receipt.generate',
                'refund.create',
                'reports.view',
            ],

            'marketing' => [
                'cms.view',
                'cms.create',
                'cms.update',
                'reports.view',
            ],

            'author' => [
                'submission.create',
                'submission.view',
                'submission.update',
                'submission.delete',
                'submission.submit',
                'submission.withdraw',
            ],

            'reader' => [],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }
    }
}
