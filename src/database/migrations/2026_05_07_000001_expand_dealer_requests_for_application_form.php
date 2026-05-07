<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealer_requests', function (Blueprint $table) {
            $table->dropUnique(['tc_no']);
        });

        Schema::table('dealer_requests', function (Blueprint $table) {
            $table->string('tc_no', 11)->nullable()->change();

            $table->string('first_name')->nullable()->after('full_name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('mobile_phone', 64)->nullable()->after('phone');

            $table->string('business_name')->nullable()->after('address');
            $table->string('address_line_1')->nullable()->after('business_name');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('postcode', 32)->nullable()->after('city');
            $table->string('country', 120)->nullable()->after('postcode');

            $table->string('business_type')->nullable()->after('country');
            $table->string('limited_company_name')->nullable()->after('business_type');
            $table->string('company_registration_number')->nullable()->after('limited_company_name');
            $table->string('vat_reg_number')->nullable()->after('company_registration_number');
            $table->string('website')->nullable()->after('vat_reg_number');
            $table->string('facebook')->nullable()->after('website');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('linkedin')->nullable()->after('twitter');

            $table->string('business_profile', 64)->nullable()->after('linkedin');
            $table->json('interest_areas')->nullable()->after('business_profile');
            $table->string('how_heard_about_us')->nullable()->after('interest_areas');
            $table->boolean('terms_accepted')->default(false)->after('how_heard_about_us');
        });

        foreach (DB::table('dealer_requests')->whereNull('first_name')->cursor() as $row) {
            $full = trim((string) $row->full_name);
            if ($full === '') {
                continue;
            }
            $parts = preg_split('/\s+/', $full, 2);
            DB::table('dealer_requests')->where('id', $row->id)->update([
                'first_name' => $parts[0] ?? '',
                'last_name' => $parts[1] ?? '',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('dealer_requests', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'mobile_phone',
                'business_name',
                'address_line_1',
                'address_line_2',
                'city',
                'postcode',
                'country',
                'business_type',
                'limited_company_name',
                'company_registration_number',
                'vat_reg_number',
                'website',
                'facebook',
                'instagram',
                'twitter',
                'linkedin',
                'business_profile',
                'interest_areas',
                'how_heard_about_us',
                'terms_accepted',
            ]);
        });

        Schema::table('dealer_requests', function (Blueprint $table) {
            $table->string('tc_no', 11)->nullable(false)->change();
            $table->unique('tc_no');
        });
    }
};
