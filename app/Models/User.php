<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Token;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Laravel\Passport\Client;
use Illuminate\Support\Carbon;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Laravel\Passport\Passport;
use App\Models\Interfaces\Filtered;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 * @property mixed $scopes
 * @method static Builder|User whereScopes($value)
 * @property string|null $email_verified_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 */
class User extends Authenticatable implements Filtered
{
    use HasApiTokens, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'scopes'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    
    protected $casts = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => 'Y-m-d H:i:s',
    ];
    
    protected $filters = [
        'name'  => 'like',
        'email' => 'like',
    ];
    
    /**
     * @param $value
     */
    public function setScopesAttribute($value)
    {
        $this->attributes['scopes'] = json_encode($value);
    }
    
    /**
     * @return mixed
     */
    public function getScopesAttribute()
    {
        return json_decode($this->attributes['scopes'], true);
    }
    
    /**
     * Get all of the user's registered OAuth clients.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany(
            Passport::clientModel(),
            'user_id'
        )->select(
            [
                'id',
                'user_id',
                'name',
                'secret',
                'revoked',
                'created_at',
                'updated_at',
            ]
        );
    }
    
    public function getFilters(): array
    {
        return $this->filters;
    }
    
}
