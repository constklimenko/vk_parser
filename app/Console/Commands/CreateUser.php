<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user  {name} {admin?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admin = $this->argument('admin') == 'admin' ? true : false;
        $name = $this->argument('name');
        $token = Str::random(40);
        User::create([
            'name'      => $name,
            'api_token' => $token,
            'is_admin'  => $admin
        ]);
        echo "User $name created with token $token";

        return true;
    }
}
