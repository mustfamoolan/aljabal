<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class EmployeeType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the roles associated with this employee type.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'employee_type_role');
    }

    /**
     * Get the users that have this employee type.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'employee_type_id');
    }
}
