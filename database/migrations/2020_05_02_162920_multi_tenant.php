<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MultiTenant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->uuid('uuid');
            $table->timestamps();
        });

        Schema::table('services', function ($table) {
            $table->foreignId('tenant_id')->after('id');
        });

        Schema::table('users', function ($table) {
            $table->foreignId('tenant_id')->after('id');
        });

        Schema::table('participants', function ($table) {
            $table->foreignId('tenant_id')->after('id');
        });

        $tenant = \App\Tenant::create([
            'name' => 'default',
        ]);

        $participants = \App\Participant::where('tenant_id', 0)->get();
        foreach ($participants as $participant)
        {
            $participant->tenant_id = 1;
            $participant->save();
        }

        $services = \App\Service::where('tenant_id', 0)->get();
        foreach ($services as $service)
        {
            $service->tenant_id = 1;
            $service->save();
        }

        $users = \App\User::where('tenant_id', 0)->get();
        foreach ($users as $user)
        {
            $user->tenant_id = 1;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
