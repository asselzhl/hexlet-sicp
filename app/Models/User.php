<?php

namespace App\Models;

use App\Presenters\UserPresenter;
use Database\Factories\UserFactory;
use Hemp\Presenter\Presentable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\User
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $github_name
 * @property string $hexlet_nickname
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Collection|Chapter[] $chapters
 * @property-read int|null $chapters_count
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read Collection|ExerciseMember[] $exerciseMembers
 * @property-read int|null $exercise_members_count
 * @property-read Collection|Exercise[] $exercises
 * @property-read int|null $exercises_count
 * @property-read Collection|ReadChapter[] $readChapters
 * @property-read int|null $read_chapters_count
 * @property-read Collection|Solution[] $solutions
 * @property-read int|null $solutions_count
 * @method static UserFactory factory(...$parameters)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Presentable;

    public string $defaultPresenter = UserPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function chapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'read_chapters')->withTimestamps();
    }

    public function readChapters(): HasMany
    {
        return $this->hasMany(ReadChapter::class);
    }

    public function exerciseMembers(): HasMany
    {
        return $this->hasMany(ExerciseMember::class);
    }

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercise_members')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class);
    }

    public function hasCompletedExercise(Exercise $exercise): bool
    {
        return $this->exerciseMembers()->whereExerciseId($exercise->id)->exists();
    }

    public function isGuest(): bool
    {
        return $this->exists === false;
    }

    public function isRegistered(): bool
    {
        return $this->exists;
    }

    public function haveRead(Chapter $chapter): bool
    {
        return $this->chapters->contains($chapter);
    }
}
