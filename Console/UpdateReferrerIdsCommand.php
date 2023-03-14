<?php

namespace Modules\ReferrerModule\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateReferrerIdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'update:referrer-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update the referrer_id column in the users table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get all users who does not have referrer_id
        $users = User::whereNull('referrer_id')->get();

        // Update the referrer_id for each user
        foreach ($users as $user) {
        $user->update(['referrer_id' => Str::uuid()->toString()]);
        }

        $this->info(__('referrermodule::messages.referrer.updated'));
    }

}
