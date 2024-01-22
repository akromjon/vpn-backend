<?php

namespace App\Models\Server;

use App\Jobs\Server\Creation;
use App\Models\Pritunl\Enum\PritunlStatus;
use App\Models\Pritunl\Pritunl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Server\Enum\CloudProviderType;
use App\Models\Server\Enum\ServerStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Server extends Model
{
    use HasFactory;
    protected $casts = [
        "status" => ServerStatus::class,
        "provider" => CloudProviderType::class,
        "config" => "array",
        "localization" => "json",
    ];

    protected static function booted()
    {
        static::updated(function (Server $server) {
            if ($server->isDirty("status") && $server->status == ServerStatus::INACTIVE) {

                $pritunls = $server->pritunls;

                foreach ($pritunls as $pritunl) {

                    $pritunl->update([
                        'status' => PritunlStatus::INACTIVE,
                    ]);

                    $pritunl->users()->update([
                        'status' => PritunlStatus::INACTIVE,
                    ]);

                }
            }
        });
    }

    public static function getSynchronizationStatus(): bool
    {
        return Cache::get("server_synchronization", false);
    }

    public static function setSynchronizationStatus(bool $status): void
    {
        Cache::put("server_synchronization", $status, now()->addDays());
    }

    public static function addServer(self $self)
    {
        Creation::dispatch($self);
    }

    public function pritunls(): HasMany
    {
        return $this->hasMany(Pritunl::class);
    }
}
