<?php

namespace App\Jobs\Pritunl;

use Akromjon\Pritunl\Pritunl as PritunlClient;
use App\Jobs\Pritunl\User\Synchronization;
use App\Models\Pritunl\Enum\InternalServerStatus;
use App\Models\Pritunl\Enum\PritunlStatus;
use App\Models\Pritunl\Pritunl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateNumberOfUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Pritunl $pritunl, protected int $numberOfUsers)
    {
        //
    }
    public function handle():void
    {
        try{

            $pritunl=$this->pritunl;

            $pritunl->update([
                "status"=>PritunlStatus::CREATING,
            ]);

            $client=PritunlClient::connect(
                ip: $pritunl->server->public_ip_address,
                username: $pritunl->username,
                password: $pritunl->password
            );

            $client->createNumberOfUsers(
                organizationId: $pritunl->organization_id,
                numberOfUsers: $this->numberOfUsers
            );

            $pritunl->update([
                "status"=>PritunlStatus::ACTIVE,
                "user_count"=>$pritunl->user_count+$this->numberOfUsers,
            ]);

            Synchronization::dispatch($pritunl);
        }
        catch(\Exception $e){

            Log::error($e->getMessage());

            $pritunl->update([
                "status"=>PritunlStatus::FAILED_TO_CREATE,
            ]);
        }
    }
}
