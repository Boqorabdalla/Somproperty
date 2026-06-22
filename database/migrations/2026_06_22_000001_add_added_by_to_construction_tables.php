<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('subcontractors', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('project_progress_reports', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('change_orders', function (Blueprint $table) {
            $table->integer('added_by')->unsigned()->nullable()->after('company_id');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        $tables = ['materials', 'equipment', 'subcontractors', 'vendors', 'purchase_orders', 'project_progress_reports', 'incident_reports', 'change_orders'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['added_by']);
                $table->dropColumn('added_by');
            });
        }
    }
};
