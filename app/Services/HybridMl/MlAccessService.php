<?php

namespace App\Services\HybridMl;

use App\Models\ClassRoom;
use App\Models\MlModel;
use App\Models\TrainingJob;
use App\Models\User;
use App\Models\UserDataset;

class MlAccessService
{
    public function canUseClass(User $user, ?int $classId): bool
    {
        if ($classId === null) {
            return true;
        }
        if ($user->isLearner()) {
            return $user->classesAsStudent()->whereKey($classId)->exists();
        }
        if ($user->isInstructor()) {
            return ClassRoom::query()->whereKey($classId)->where('instructor_id', $user->id)->exists();
        }
        return $user->isPlatformStaff();
    }

    public function canViewUserDataset(User $user, UserDataset $dataset): bool
    {
        if ((int) $dataset->user_id === (int) $user->id) {
            return true;
        }
        return $user->isInstructor()
            && $dataset->class_id !== null
            && ClassRoom::query()->whereKey($dataset->class_id)->where('instructor_id', $user->id)->exists();
    }

    public function canViewModel(User $user, MlModel $model): bool
    {
        if ($model->isSystemModel()) {
            return true;
        }
        if ((int) $model->user_id === (int) $user->id) {
            return true;
        }
        return $user->isInstructor()
            && $model->class_id !== null
            && ClassRoom::query()->whereKey($model->class_id)->where('instructor_id', $user->id)->exists();
    }

    public function canViewTrainingJob(User $user, TrainingJob $job): bool
    {
        if ((int) $job->user_id === (int) $user->id) {
            return true;
        }
        return $user->isInstructor()
            && $job->class_id !== null
            && ClassRoom::query()->whereKey($job->class_id)->where('instructor_id', $user->id)->exists();
    }
}
