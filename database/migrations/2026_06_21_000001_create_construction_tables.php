<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('current_stock', 15, 2)->default(0);
            $table->decimal('min_stock', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('material_categories')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('set null');
        });

        Schema::create('material_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('material_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->enum('type', ['added', 'removed', 'adjusted'])->default('added');
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('quantity_after', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('equipment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('equipment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name');
            $table->integer('equipment_type_id')->unsigned()->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->enum('status', ['available', 'in-use', 'under-maintenance', 'retired'])->default('available');
            $table->string('location')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('equipment_type_id')->references('id')->on('equipment_types')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });

        Schema::create('equipment_maintenance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('equipment_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('description');
            $table->date('maintenance_date');
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('vendor')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('subcontractors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('trade_type')->nullable();
            $table->string('license_no')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('subcontractor_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('subcontractor_id')->unsigned();
            $table->string('name');
            $table->string('filename');
            $table->string('hashname');
            $table->string('size')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade');
        });

        Schema::create('project_subcontractors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('subcontractor_id')->unsigned();
            $table->decimal('contract_amount', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade');
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('po_number')->unique();
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->date('order_date');
            $table->date('expected_delivery')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'received'])->default('draft');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('currency_id')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_order_id')->unsigned();
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->timestamps();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });

        Schema::create('project_progress_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned();
            $table->integer('milestone_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->date('report_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('work_summary')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('milestone_id')->references('id')->on('project_milestones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('progress_report_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('progress_report_id')->unsigned();
            $table->string('filename');
            $table->string('hashname');
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('progress_report_id')->references('id')->on('project_progress_reports')->onDelete('cascade');
        });

        Schema::create('incident_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('incident_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('title');
            $table->date('incident_date');
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->text('description');
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('incident_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('incident_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incident_id')->unsigned();
            $table->string('filename');
            $table->string('hashname');
            $table->string('size')->nullable();
            $table->timestamps();
            $table->foreign('incident_id')->references('id')->on('incident_reports')->onDelete('cascade');
        });

        Schema::create('change_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('change_order_number')->unique();
            $table->integer('project_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('currency_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('change_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('change_order_id')->unsigned();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('change_order_id')->references('id')->on('change_orders')->onDelete('cascade');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('budget_amount', 15, 2)->nullable()->after('project_admin');
            $table->integer('budget_currency_id')->unsigned()->nullable()->after('budget_amount');
            $table->foreign('budget_currency_id')->references('id')->on('currencies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['budget_currency_id']);
            $table->dropColumn(['budget_amount', 'budget_currency_id']);
        });

        Schema::dropIfExists('change_order_items');
        Schema::dropIfExists('change_orders');
        Schema::dropIfExists('incident_files');
        Schema::dropIfExists('incident_reports');
        Schema::dropIfExists('incident_categories');
        Schema::dropIfExists('progress_report_photos');
        Schema::dropIfExists('project_progress_reports');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('project_subcontractors');
        Schema::dropIfExists('subcontractor_documents');
        Schema::dropIfExists('subcontractors');
        Schema::dropIfExists('equipment_maintenance');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('equipment_types');
        Schema::dropIfExists('material_inventories');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('material_categories');
    }
};
