<?php

namespace App\Policies;

use App\Models\SupportPlan;
use App\Models\User;
use App\Models\Team;
use Illuminate\Auth\Access\Response;

class SupportPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can see the index page
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupportPlan $supportPlan): bool
    {
        // User can view if they created the plan
        if ($supportPlan->user_id === $user->id) {
            return true;
        }

        // User can view if the plan belongs to a team they're part of
        if ($supportPlan->team_id && $this->userHasTeamAccess($user, $supportPlan->team_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create plans
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupportPlan $supportPlan): bool
    {
        // User can update if they created the plan
        if ($supportPlan->user_id === $user->id) {
            return true;
        }

        // User can update if they are an admin in the team the plan belongs to
        if ($supportPlan->team_id) {
            $team = Team::find($supportPlan->team_id);
            if ($team && ($team->owner_id === $user->id || $team->hasUserWithRole($user, 'admin'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupportPlan $supportPlan): bool
    {
        // User can delete if they created the plan
        if ($supportPlan->user_id === $user->id) {
            return true;
        }

        // User can delete if they are the team owner
        if ($supportPlan->team_id) {
            $team = Team::find($supportPlan->team_id);
            if ($team && $team->owner_id === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupportPlan $supportPlan): bool
    {
        return $this->delete($user, $supportPlan);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupportPlan $supportPlan): bool
    {
        return $this->delete($user, $supportPlan);
    }

    /**
     * Check if user has access to a team.
     */
    private function userHasTeamAccess(User $user, int $teamId): bool
    {
        $team = Team::find($teamId);
        return $team && ($team->hasUser($user) || $team->owner_id === $user->id);
    }
}
