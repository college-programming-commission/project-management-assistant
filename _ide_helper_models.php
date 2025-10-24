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


namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $name
 * @property int|null $freezing_period
 * @property int|null $course_number
 * @property int $period
 * @property array<array-key, mixed>|null $attachments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category byCourseNumber(int $courseNumber)
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category maxFreezingPeriod(int $days)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category maxPeriod(int $days)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category minFreezingPeriod(int $days)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category minPeriod(int $days)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category searchByName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCourseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereFreezingPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withSubject(string|int $subjectId)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCategory {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $category_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $bg_color
 * @property string|null $fg_color
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Alison\ProjectManagementAssistant\Models\Category $category
 * @property-read mixed $description_html
 * @property-read mixed $description_preview
 * @property-read string|null $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Subevent> $subevents
 * @property-read int|null $subevents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Supervisor> $supervisors
 * @property-read int|null $supervisors_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event betweenDates(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event byCategory(string|int $categoryId)
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event past()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event searchByName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event upcoming()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereFgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEvent {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $project_id
 * @property string $sender_id
 * @property string $message
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $message_html
 * @property-read mixed $message_preview
 * @property-read \Alison\ProjectManagementAssistant\Models\Project $project
 * @property-read \Alison\ProjectManagementAssistant\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message byIsRead(bool $isRead)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message byProject(string|int $projectId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message bySender(string|int $senderId)
 * @method static \Database\Factories\MessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMessage {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $project_id
 * @property string $student_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Alison\ProjectManagementAssistant\Models\Project $project
 * @property-read \Alison\ProjectManagementAssistant\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer byProject(string|int $projectId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer byStudent(string|int $studentId)
 * @method static \Database\Factories\OfferFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer latestOffers(int $limit = 5)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer recent(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOffer {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string|null $event_id
 * @property string|null $supervisor_id
 * @property string|null $assigned_to
 * @property string $slug
 * @property string $name
 * @property string|null $appendix
 * @property string|null $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Alison\ProjectManagementAssistant\Models\User|null $assignedTo
 * @property-read mixed $body_html
 * @property-read mixed $body_preview
 * @property-read \Alison\ProjectManagementAssistant\Models\Event|null $event
 * @property-read int $event_projects_count
 * @property-read bool $has_unread_messages
 * @property-read int $unread_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Offer> $offers
 * @property-read int|null $offers_count
 * @property-read \Alison\ProjectManagementAssistant\Models\Supervisor|null $supervisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Technology> $technologies
 * @property-read int|null $technologies_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byAssignedStudent(string|int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byEvent(string|int $eventId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project bySupervisor(string|int $supervisorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project byTechnology(string|int $technologyId)
 * @method static \Database\Factories\ProjectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project orderByCreated(string $direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project orderByName(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project searchByNameOrBody(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereAppendix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withAssignedTo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withSupervisor()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withoutAssignedTo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project withoutSupervisor()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProject {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $event_id
 * @property string|null $depends_on
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $bg_color
 * @property string|null $fg_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subevent> $dependentSubevents
 * @property-read int|null $dependent_subevents_count
 * @property-read Subevent|null $dependsOn
 * @property-read mixed $description_html
 * @property-read mixed $description_preview
 * @property-read \Alison\ProjectManagementAssistant\Models\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent betweenDates(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent byEvent(string|int $eventId)
 * @method static \Database\Factories\SubeventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent searchByDescription(string $text)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent searchByName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereDependsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereFgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subevent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubevent {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property int|null $course_number
 * @property string|null $description
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read mixed $description_html
 * @property-read mixed $description_preview
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject byCategory(string|int $categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject byCourse(int $course)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject byName(string $name)
 * @method static \Database\Factories\SubjectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject searchByDescription(string $text)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject searchByName(string $text)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereCourseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubject {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $event_id
 * @property string $user_id
 * @property string|null $note
 * @property int|null $slot_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Alison\ProjectManagementAssistant\Models\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Alison\ProjectManagementAssistant\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor activeEvent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor byEvent(string|int $eventId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor bySlotCount(int $slotCount)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor byUser(string|int $userId)
 * @method static \Database\Factories\SupervisorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor maxSlotCount(int $slots)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor minSlotCount(int $slots)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor searchByNote(string $text)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereSlotCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSupervisor {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $description_html
 * @property-read mixed $description_preview
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Project> $projects
 * @property-read int|null $projects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology byName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology byProject(string|int $projectId)
 * @method static \Database\Factories\TechnologyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology searchByDescription(string $text)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology withLink()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Technology withoutLink()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTechnology {}
}

namespace Alison\ProjectManagementAssistant\Models{
/**
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $google_id
 * @property string|null $remember_token
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string|null $description
 * @property string|null $avatar
 * @property int|null $course_number
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property-read mixed $description_html
 * @property-read mixed $description_preview
 * @property-read string $full_name
 * @property-read string $name
 * @property-read string $short_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Offer> $offers
 * @property-read int|null $offers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \NotificationChannels\WebPush\PushSubscription> $pushSubscriptions
 * @property-read int|null $push_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Alison\ProjectManagementAssistant\Models\Supervisor> $supervisors
 * @property-read int|null $supervisors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User alphabetically()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byCourse(int $course)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byEmailDomain(string $domain)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byName(string $name)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byRole(string $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User hasOffers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User isSupervisor()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User limitUsers(int $limit)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User recentFirst()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCourseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

