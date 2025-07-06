<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // member_details
        Schema::create('member_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('gender');
            $table->date('dob')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('ref_by')->nullable();
            $table->string('address')->nullable();
            $table->string('profile_photo')->nullable();
            $table->timestamps();
        });


        // store plan types
        Schema::create('plan_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('users')->onDelete('cascade');
            $table->string('plan_name');
            $table->timestamps();
        });


        // gym_plan
        Schema::create('gym_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('member_details')->onDelete('cascade');
            $table->string('plan_name');
            $table->string('period');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('price');
            $table->timestamps();
        });


        // personal tranier
        Schema::create('personal_trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('member_details')->onDelete('cascade');
            $table->foreignId('gym_id')->constrained('users')->onDelete('cascade');
            $table->string('pt_name');
            $table->string('period');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('price');
            $table->timestamps();
        });


        // payment
        Schema::create('member_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('member_details')->onDelete('cascade');
            $table->string('total_amount');
            $table->string('paid_amount');
            $table->string('payment_status');
            $table->string('installment')->nullable();
            $table->string('next_payment_amount')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_payment');
        Schema::dropIfExists('personal_trainers');
        Schema::dropIfExists('gym_plan');
        Schema::dropIfExists('plan_names');
        Schema::dropIfExists('member_details');
    }
};
