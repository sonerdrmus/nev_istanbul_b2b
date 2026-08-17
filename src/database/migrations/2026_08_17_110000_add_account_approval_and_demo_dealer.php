<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'is_approved')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_approved')->default(false)->after('is_admin');
            });

            DB::table('users')->update(['is_approved' => true]);
        }

        if (Schema::hasTable('dealer_requests') && ! Schema::hasColumn('dealer_requests', 'password')) {
            Schema::table('dealer_requests', function (Blueprint $table) {
                $table->string('password')->nullable()->after('email');
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        if (Schema::hasTable('footer_menu_items')) {
            DB::table('footer_menu_items')
                ->where('label', 'Hesabım')
                ->update(['url' => '/hesabim']);
        }

        $company = Company::firstOrCreate(
            ['code' => 'DEMO-DEALER'],
            ['name' => 'Demo Bayi', 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'dealer@demo.com'],
            [
                'company_id' => $company->id,
                'name' => 'Demo Bayi',
                'password' => 'password',
                'is_admin' => false,
                'is_approved' => true,
            ]
        );

        User::query()->where('email', 'admin@admin.com')->update(['is_admin' => true, 'is_approved' => true]);
        User::query()->where('email', 'customer@demo.com')->update(['is_admin' => false, 'is_approved' => true]);
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if (Schema::hasTable('dealer_requests') && Schema::hasColumn('dealer_requests', 'password')) {
            Schema::table('dealer_requests', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_approved')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_approved');
            });
        }
    }
};
