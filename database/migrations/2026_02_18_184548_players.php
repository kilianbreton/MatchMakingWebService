<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->down();

        //Gamemodes ===========================================================================================================
        Schema::create('gamemodes', function (Blueprint $table)
        {
            $table->id();
            $table->string('name');
            $table->string('titlepack');
            $table->string('lobbyscript');
            $table->string('matchscript');
        });

        DB::table('gamemodes')->insert([
            ['name' => 'Elite', 'titlepack' => 'SMStormElite@nadeolabs', 'lobbyscript' => 'ElitePro.Script.txt', 'matchscript' => 'ElitePro.Script.txt'],
        ]);


        //StatsInfos ==========================================================================================================
        Schema::create('statsinfos', function (Blueprint $table)
        {
            $table->id();
            $table->string('name');
        });

        DB::table('statsinfos')->insert([
            ["name" => "Shots"],
            ["name" => "Hits"],
            ["name" => "Near Misses"],
            ["name" => "Captures"],
            ["name" => "Deaths"],
            ["name" => "Kills"],
            ["name" => "Laser Hits"],
            ["name" => "Laser Shots"],
            ["name" => "Nucleus Hits"],
            ["name" => "Nucleus Shots"],
            ["name" => "Rocket Hits"],
            ["name" => "Rocket Shots"],
            ["name" => "Arrow Hits"],
            ["name" => "Arrow Shots"],
            ["name" => "Wins"],
            ["name" => "Attacks"],
            ["name" => "Successful Attack"],
        ]);



        //Players =============================================================================================================
        Schema::create('players', function (Blueprint $table)
        {
            $table->id();
            $table->string('login')->unique();
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->integer('rank')->default(1000);
            $table->string('token')->nullable();
            $table->string('refresh')->nullable();
            $table->timestamps();
        });


        //Servers =============================================================================================================
        Schema::create('servers', function (Blueprint $table)
        {
            $table->id();
            $table->string('login')->unique();
            $table->string('name')->nullable();
            $table->integer('gamemode')->nullable();
            $table->integer('type')->nullable();
            $table->timestamp('latestping')->nullable();
            $table->foreignId('ownerid')->constrained('players')->cascadeOnDelete();
            $table->string('apikey')->nullable();
            $table->integer('nbplayers')->default(0);
            $table->string('score')->nullable();
            $table->foreignId('lobbyid')->nullable()->constrained('servers');
            $table->timestamps();
        });



        //Maps ==============================================================================================================
        Schema::create('maps', function (Blueprint $table)
        {
            $table->string('uid', 255)->primary();
            $table->integer('mxid')->nullable();
            $table->string('name');
            $table->string('author')->nullable();
            $table->timestamps();
        });



        //Match =============================================================================================================
        Schema::create('matches', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('serverid')->constrained('servers');
            $table->foreignId('gamemodeid')->nullable()->constrained('gamemodes');
            $table->string('score')->nullable();
            $table->string('mapuid')->nullable();
            $table->integer('winner')->nullable();
            $table->boolean('finished')->default(false);
            $table->timestamps();
        });



        //MatchPlayers ==========================================================================================================
        Schema::create('matchplayers', function (Blueprint $table)
        {
            $table->foreignId('matchid')->constrained("matches")->cascadeOnDelete();
            $table->foreignId('playerid')->constrained("players")->cascadeOnDelete();
            $table->integer('team');
            $table->integer('playorder');
            $table->boolean('missing')->default(false);
            $table->boolean('replaced')->default(false);

            $table->primary(['matchid', 'playerid']);
        });



        //Statistics ==========================================================================================================
        Schema::create('statistics', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('type')->constrained('statsinfos')->cascadeOnDelete();
            $table->foreignId('playerid')->constrained('players')->cascadeOnDelete();
            $table->foreignId('matchid')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('serverid')->nullable()->constrained('servers');
            $table->foreignId('gamemodeid')->nullable()->constrained('gamemodes');
            $table->string('mapuid', 255)->nullable(); // Assure une bonne longueur pour correspondre à `maps.uid`
            $table->foreign('mapuid')->references('uid')->on('maps')->onDelete('set null');
            $table->integer('value');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchplayers');
        Schema::dropIfExists('statistics');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('servers');
        Schema::dropIfExists('players');
        Schema::dropIfExists('maps');
        Schema::dropIfExists('gamemodes');
        Schema::dropIfExists('statsinfos');

    }
};
