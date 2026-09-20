<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gym> $gyms
 * @property-read int|null $gyms_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $gym_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Gym $gym
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereUpdatedAt($value)
 */
	class Facility extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $address
 * @property \App\Enums\Region $region
 * @property string|null $district
 * @property string|null $phone
 * @property string|null $whatsapp
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string $opening_hours
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $approvedReviews
 * @property-read int|null $approved_reviews_count
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facility> $facilities
 * @property-read int|null $facilities_count
 * @property-read mixed $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymImage> $images
 * @property-read int|null $images_count
 * @property-read mixed $maps_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GymPackage> $packages
 * @property-read int|null $packages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read mixed $whatsapp_url
 * @method static \Database\Factories\GymFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym inCategory(?string $categorySlug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym inRegion(?string $region)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym search(?string $keyword)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereOpeningHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym whereWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gym withDistance(float $lat, float $lng)
 */
	class Gym extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $gym_id
 * @property string $image
 * @property string|null $caption
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $url
 * @property-read \App\Models\Gym $gym
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymImage whereUpdatedAt($value)
 */
	class GymImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $gym_id
 * @property string $name
 * @property string $duration
 * @property int $price
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_price
 * @property-read \App\Models\Gym $gym
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GymPackage whereUpdatedAt($value)
 */
	class GymPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $gym_id
 * @property int $rating
 * @property string $review
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Gym $gym
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

