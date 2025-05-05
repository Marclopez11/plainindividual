<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'student_name',
        'birth_date',
        'birth_place',
        'phone',
        'address',
        'educational_level',
        'course',
        'group',
        'school_year',
        'tutor_name',
        'teacher_name',
        'assessment_team',
        'revision_date',
        'elaboration_date',
        'family_agreement_date',
        'other_data',
        'usual_language',
        'other_languages',
        'school_incorporation_date',
        'catalonia_arrival_date',
        'educational_system_date',
        'previous_schools',
        'previous_schooling',
        'course_retention',
        'justification_reasons',
        'justification_other',
        'commission_proponent',
        'commission_motivation',
        'brief_justification',
        'competencies_alumne_capabilities',
        'learning_strong_points',
        'learning_improvement_points',
        'student_interests',
        'transversal_objectives',
        'transversal_criteria',
        'learning_objectives',
        'evaluation_criteria',
        'area_materia',
        'bloc_sabers',
        'saber',
        'team_id',
        'user_id',
        'professionals',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'revision_date' => 'date',
        'elaboration_date' => 'date',
        'family_agreement_date' => 'date',
        'school_incorporation_date' => 'date',
        'catalonia_arrival_date' => 'date',
        'educational_system_date' => 'date',
        'justification_reasons' => 'array',
        'transversal_objectives' => 'array',
        'transversal_criteria' => 'array',
        'learning_objectives' => 'array',
        'evaluation_criteria' => 'array',
        'area_materia' => 'array',
        'bloc_sabers' => 'array',
        'saber' => 'array',
        'professionals' => 'array',
    ];

    /**
     * Get the team that owns the support plan.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user that owns the support plan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the timetables for the support plan.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}
